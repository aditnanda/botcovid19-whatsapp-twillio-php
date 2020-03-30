<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Twilio\TwiML\MessagingResponse;
use GuzzleHttp\Client;


const GOOD_BOY_URL = "https://images.unsplash.com/photo-1518717758536-85ae29035b6d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1350&q=80";

class WhatsappController extends Controller
{

    public function __construct()
    {
        $this->http = new Client();
    }

    public function method($type,$url){
        return json_decode($this->http->request($type, $url)->getBody()->getContents());
    }

    public function webhook(Request $request) {

        // Get number of images in the request
        $numMedia = (int) $request->input("NumMedia");

        $body = $request->input("Body");

        Log::debug("Media files received: {$request}");

        $response = new MessagingResponse();
        // if ($numMedia === 0) {
        //     $message = $response->message("Ketik /body");

        // } else {
        //     $message = $response->message("Terimakasih, ini foto untukmu");
        //     $message->media(GOOD_BOY_URL);
        // }

        if (strtolower($body) == 'global') {
            $global = $this->method('GET', 'http://apicovid19.aditnanda.com/negara/semua');
            $message = $response->message("*Data Covid19 di Dunia*\n\n-Terkonfirmasi : ".$global->cases."\n-Sembuh : ".$global->recovered."\n-Meninggal : ".$global->deaths);
        }else if (strtolower($body) == 'indonesia'){
            $global = $this->method('GET', 'http://apicovid19.aditnanda.com/negara/indonesia');
            $message = $response->message("*Data Covid19 di Indonesia*\n\n-Terkonfirmasi : ".$global->cases."\n-Sembuh : ".$global->recovered."\n-Meninggal : ".$global->deaths);
        }else if (strtolower($body) == 'bantuan'){
            $message = $response->message("*INFO COVID 19*\n\nKetik\n1. global : untuk informasi covid19 di dunia\n2. indonesia : untuk informasi covid19 di Indonesia\n\nMade by Aditya Nanda");
        }else{
            $message = $response->message("Ketik bantuan");
        }

        return $response;
    }

}
