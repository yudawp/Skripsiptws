<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Allfile;
use Illuminate\Support\Facades\Mail;
use App\Mail\AesMail;
use App\Helpers\AesHelper;
use Illuminate\Support\Facades\Crypt;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->blowkey = '3a39!-%8'; //key untuk blowfish
    }
    function blowencrypt($data) {
        $size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_CBC); //mengambil size untuk blowfish 64 bit
        $iv = mcrypt_create_iv($size, MCRYPT_RAND); //membagi 2 size 64 bit menjadi masing masing 32 bit
        $crypt = mcrypt_encrypt(MCRYPT_BLOWFISH, $this->blowkey, $data, MCRYPT_MODE_CBC, $iv); //proses enkripsi algoritma blowfish
        return bin2hex($iv . $crypt); //menerjemahkan hexadesimal dari hasil iv dan crypt
    }
    function blowdecrypt($data) {
        $iv = pack("H*", substr($data, 0, 16)); //menggabungkan size 16 bit left
        $x = pack("H*", substr($data, 16)); //menggabungkan size 16 bit right
        $res = mcrypt_decrypt(MCRYPT_BLOWFISH, $this->blowkey, $x, MCRYPT_MODE_CBC, $iv); //proses dekripsi blowfish
        return $res; //mengembalikan hasil dari dekripsi
    }
    function random_str($type = 'alphanum', $length = 8)
    {
        switch($type)
        {
            case 'basic'    : return mt_rand();
                break;
            case 'alpha'    :
            case 'alphanum' :
            case 'num'      :
            case 'nozero'   :
                    $seedings             = array();
                    $seedings['alpha']    = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $seedings['alphanum'] = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $seedings['num']      = '0123456789';
                    $seedings['nozero']   = '123456789';
                    
                    $pool = $seedings[$type];
                    
                    $str = '';
                    for ($i=0; $i < $length; $i++)
                    {
                        $str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
                    }
                    return $str;
                break;
            case 'unique'   :
            case 'md5'      :
                    return md5(uniqid(mt_rand()));
                break;
        }
    }
    public function index()
    {
        
        $files = Allfile::count();
        return view('home',compact('files'));
        //phpinfo();
    }
    public function allfile()
    {
        $files = Allfile::orderBy('created_at','desc')->paginate(5);
        return view('allfile',compact('files'));
    }
    public function addfile()
    {
        return view('addfile');
    }
    public function uploadfile(Request $request)
    {
        //Mulai Itung
        $time_start = microtime(true);

        $this->validate($request, [
            'name' => 'required',
            'file' => 'required|file',
        ]);
        // Rename File
        $orifilename = basename($request->file('file')->getClientOriginalName(), '.'.$request->file('file')->getClientOriginalExtension());
        $guessExtension = $request->file('file')->guessExtension();
        $filename = $this->blowencrypt($orifilename).'.'.$guessExtension;
        $file = $request->file('file')->storeAs('files', $filename ,'public');
        // dd($filename);
        // Save to DB
        $newfile = new Allfile;
        $newfile->name = $request->name;
        $newfile->filename = $filename;
        $newfile->save();

        // Selesai Itung
        $time_end = microtime(true);

        // Hasil Itung
        $time = $time_end - $time_start;

        // Tambah variable $time
        return back()->with('status',"File Berhasil Ditambahkan! Execution time : $time second");
    }
    /*public function download($file='')
    {
        $ex = explode('.',$file);
        $decrypt = $this->blowdecrypt($ex[0]);
        $decrypt = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $decrypt);
        $filename = $decrypt.'.'.$ex[1];
        return response()->download(storage_path('app/public/files/'.$file), $filename);
    }*/
    public function download($file='')
    {
        // Mulai Itung
        $time_start = microtime(true);

        $ex = explode('.',$file);
        $decrypt = $this->blowdecrypt($ex[0]);
        $decrypt = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $decrypt);
        // $filename = $decrypt.'.'.$ex[1];

        // Selesai Itung
        $time_end = microtime(true);

        // Hasil Itung
        $time = $time_end - $time_start;

        // paling hasil Execution time supaya tau taro di nama file nya, jadi gini:
        $filename = "$decrypt - $time.$ex[1]";

        return response()->download(storage_path('app/public/files/'.$file), $filename);
    }
    /*public function unduh($id, Request $request)
    {  
        $file = Allfile::findOrFail($id);
        // Flush Session
        $request->session()->forget($request->user()->id);
        $otp = $this->random_str();
        // AES Encrypt

        $enc = encrypt($otp);
        // Put to Session
        session([$request->user()->id => $enc]);

        // Send Email
        Mail::to($request->user()->email)->send(new AesMail($otp));

        return view('unduh',compact('file'));
    }*/
    public function unduh($id, Request $request)
    {  
        $file = Allfile::findOrFail($id);
        // Flush Session
        $request->session()->forget($request->user()->id);
        $otp = $this->random_str();
        // Mulai Itung
        $time_start = microtime(true);
        // AES Encrypt
        $enc = encrypt($otp);
        // Selesai Itung
        $time_end = microtime(true);
        // Hasil Itung
        $time = $time_end - $time_start;
        // Put to Session
        session([$request->user()->id => $enc]);

        // Send Email
        Mail::to($request->user()->email)->send(new AesMail($otp));

        return view('unduh',compact('file','time'));
    }
    public function actUnduh(Request $request)
    {
        /*$key = session($request->user()->id);
        // Aes Decrypt
        
        $dec = decrypt($key);
        // Flush Session
        $request->session()->forget($request->user()->id);

        if ($request->otp == $dec) {
            $getfile = Allfile::findOrFail($request->id);
            $file = $getfile->filename;
            return redirect('files/'.$file);
        } else {
            return redirect('allfile')->with('status', 'Kode OTP Tidak Valid Atau Sudah Expired!');
        }*/

        $key = session($request->user()->id);

        // Mulai Itung
        $time_start = microtime(true);
        // Aes Decrypt
        $dec = decrypt($key);
        // Selesai Itung
        $time_end = microtime(true);
        // Hasil Itung
        $time = $time_end - $time_start;

        // Flush Session
        $request->session()->forget($request->user()->id);

        if ($request->otp == $dec) {
            $getfile = Allfile::findOrFail($request->id);
            $file = $getfile->filename;
            return redirect('files/'.$file.'?loadtime='.$time);
        } else {
            return redirect('allfile')->with('status', 'Kode OTP Tidak Valid Atau Sudah Expired!');
        }

    }
}
