<?php
require_once ('alomencoder.obfs.php');

if (!is_dir('data'))
{
	mkdir('data');
}

define('API_KEY', ''); //Bot Token

function bot($method, $datas = [])
{
	$ch = curl_init();
	curl_setopt_array($ch, array(
		CURLOPT_URL => 'https://api.telegram.org/bot' . API_KEY . '/' . $method,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POSTFIELDS => $datas
	));
	return json_decode(curl_exec($ch));
}
function format($file_name)
{
	$explode = explode('.', $file_name);
	$e = count($explode) - 1;
	return strtolower($explode[$e]);
}
function encoder($file, $obfs_file)
{
	$obfs = AlomEncoder::obfuscator($file);
	file_put_contents($obfs_file, $obfs);
}
function randName($format)
{
	$string = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') , 0, 20);
	$encode = md5($string);
	return $encode . '.' . $format;
}
function editmessage($msg, $text, $key = null, $markdown = 'html')
{
	global $chat_id;
	return request('editMessageText', ['chat_id' => $chat_id, 'text' => $text, 'reply_markup' => $key, 'message_id' => $msg, 'parse_mode' => $markdown]);
}
$update = json_decode(file_get_contents('php://input'));
if (isset($update->message))
{
	$text = $update->message->text;
	$chat_id = $update->message->chat->id;
	$from_id = $update->message->from->id;
	$message_id = $update->message->message_id;
	$tc = $update->message->chat->type;

	if ($tc == 'private')
	{
		if ($text == '/start')
		{
			bot('sendmessage', ['chat_id' => $from_id, 'text' => "👋 | سلام به ربات اینکدر Php خوش اومدی. 

🔐 | لطفا برای رمزگذاری، فایل را با فرمت php ارسال کنید.

🥷 | قدرت گرفته از Alom , با تشکر از @av_id عزیز", 'reply_markup' => json_encode(['inline_keyboard' => [[['text' => "⭐️| Encoder ALOM", 'callback_data' => "help"],['text' => "👨🏻‍💻| Support", 'url' => "https://t.me/ixAmirCom"]], ]]) ]);
		}
		elseif (isset($update->message->document))
		{
			$file_size = $update->message->document->file_size;
			$file_name = $update->message->document->file_name;
			if ($file_size <= 100550)
			{
				$format = format($file_name);
				if ($format == 'php')
				{
					bot('sendmessage', ['chat_id' => $from_id, 'text' => "در حال انجام .. لطفا صبر کنید.\nIn progress.. Please wait.", 'parse_mode' => 'HTML', ]);
					$put_name = randName($format);
					$get = bot('getFile', ['file_id' => $update->message->document->file_id]);
					$fopen = fopen('https://api.telegram.org/file/bot' . API_KEY . '/' . $get->result->file_path, 'r');
					encoder($fopen, 'data/' . $put_name);
					bot('sendDocument', ['chat_id' => $from_id, 'document' => new CURLFile('data/' . $put_name) , 'caption' => "✅ | فایل شما با موفقیت اینکد شد.

➖ encoded by ALOM v3.0 ( ⭐️ )

@AlomEncoderBot", ]);
					unlink('data/' . $put_name);
				}
				else bot('sendmessage', ['chat_id' => $from_id, 'text' => "لطفا فایل را فقط با فرمت .php ارسال کنید❗️\nPlease send the file in .php format only", 'parse_mode' => 'HTML', ]);
			}
			else bot('sendmessage', ['chat_id' => $from_id, 'text' => "حجم فایل باید کمتر از 100 کیلوبایت باشد.❗️\nThe file size should be less than 100 KB", 'parse_mode' => 'HTML', ]);
		}
		else bot('sendmessage', ['chat_id' => $from_id, 'text' => "لطفا فقط یک فایل ارسال کنید. ⚠️\nPlease send only one file", 'parse_mode' => 'HTML', ]);
	}
}
else
	$data = $update->callback_query->data;
	$chat_id = $update->callback_query->message->chat->id;
	$from_id = $update->callback_query->from->id;
	$chattype = $update->callback_query->chat->type;
	$message_id = $update->callback_query->message->message_id;

	if($data=="help"){
	bot('editMessageText',[
  'chat_id'=>$from_id,
  'message_id'=>$message_id,
  'text'=>"به صفحه ی معرفی Alom Encoder خوش اومدی ...!❤️

در اینجا میتونی بیشتر با این اینکدر آشنا بشی✅",
  'reply_markup'=> json_encode([
    'inline_keyboard' => [
      [['text' => "صفحه ی اصلی", 'url' => "https://github.com/avid0/Alom"]],
      [['text' => "لایسنس گذاری", 'url' => "https://github.com/avid0/Alom#license-settings"],['text' => "کد اضافه", 'url' => "https://github.com/avid0/Alom#additional-settings"]],
      [['text' => "تنظیمات هویتی", 'url' => "https://github.com/avid0/Alom#identfy-settings"],['text' => "لایه های امنیتی", 'url' => "https://github.com/avid0/Alom#rounds-settings"]],
      [['text' => "استایل", 'url' => "https://github.com/avid0/Alom#style-settings"]],
      [['text' => 'بازگشت','callback_data' => 'back']],
      ]])
      ]);
	}
if($data=="back"){
    bot('editMessageText',[
    'chat_id'=>$from_id,
    'message_id'=>$message_id,
    'text'=>"👋 | سلام به ربات اینکدر Php خوش اومدی. 

🔐 | لطفا برای رمزگذاری، فایل را با فرمت php ارسال کنید.

🥷 | قدرت گرفته از Alom , با تشکر از @av_id عزیز",
    'reply_markup'=>json_encode([
    'inline_keyboard'=>[
        [['text' => "⭐️| Encoder ALOM", 'callback_data' => "help"],['text' => "👨🏻‍💻| Support", 'url' => "https://t.me/ixAmirCom"]],
    ]
    ])
    ]);
    }
