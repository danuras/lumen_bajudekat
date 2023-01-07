<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\User;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;

class EmailController extends BaseController
{
    public function send()
    {
        Mail::to('darussalaamnurrasyidu@gmail.com')->send(new SendEmail('hehe'));
        return 'Mail sent';
    }
}
