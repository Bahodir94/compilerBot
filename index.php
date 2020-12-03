<?php
$conn = mysqli_connect("localhost", "USER", "PASSWORD","DBNAME");
$token = "BOT TOKEN";

function bot ($method, $data=[]){
    $ch = curl_init();
    $url = "https://api.telegram.org/bot".$token."/".$method;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_POST, 1);
    
    $headers = array();
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close ($ch);
    
    return $result;
}


function compile($data){
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, 'https://rextester.com/rundotnet/api');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_POST, 1);
    
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close ($ch);
    
    $data = json_decode($result);
    return $data;
}

$update = json_decode(file_get_contents("php://input"));

$inline_comp = json_encode([
    'inline_keyboard' => [
        [['text' => "🔄 Kodni yangilash", 'callback_data' => 'recode']],
        [['text' => "🔄 Kiruvchi ma'lumotlarni yangilash", 'callback_data' => 'reinput']],
        [['text' => "🆗 Start 🆗", 'callback_data' => 'run']],

    ]
]);
$langs = [
    '27'=>'C++',
    '1' => 'C#',
    '4'=>'Java',
    '24'=>'Python',
    '8'=>'Php',
    '17'=>'Javascript',
    '33'=>'MySQL',
    '9'=>'Pascal'
];
if ($update != Null){
    if ($update->callback_query){
        $callback = $update->callback_query;
        $data = $callback->data;
        $chatId = $callback->message->chat->id;
        $callbackQueryId = $callback->id;
        $messageId = $callback->message->message_id;
        $res = (mysqli_query($conn, "SELECT * FROM telegram WHERE user_id='$chatId'"))->fetch_array(MYSQLI_ASSOC);
        if ($res){
            $step = $res['step'];
            $lang = $res['lang'];
            $input = $res['input'];
            $code = $res['code'];

            if ($lang == 27) $args = "-std=c++14 -O2 -o a.out source_file.cpp";
            else $args = "";

            if ($lang == 1) {
            	$code = "using System; using System.Collections.Generic;using System.Linq;using System.Text.RegularExpressions;namespace Rextester{public class Program{public static void Main(string[] args){\n".$code." }}}";
            }

            $arr = [
                "Program" => $code,
                "LanguageChoice" => $lang,
                "Input" => $input,
                "CompilerArgs" => $args,
            ];
        }
        if (is_numeric($data)){
            $sql = "UPDATE telegram SET lang=$data WHERE user_id='$chatId'";
            mysqli_query($conn, $sql);

            bot("answerCallbackQuery", [
                'callback_query_id' => $callbackQueryId,
                'text' => "✅ Dasturlash tili saqlandi!",
                'show_alert' => true
            ]);
            bot("deleteMessage", [
                'chat_id' => $chatId,
                'message_id' => $messageId,
            ]);
            exit();
        }
        if ($data == 'recode'){
            $sql = "UPDATE telegram SET step=5 WHERE user_id='$chatId'";
            mysqli_query($conn, $sql);
            bot("sendMessage", [
                'chat_id' => $chatId,
                'text' => "<b>Kompilyatsiya uchun kodingizni yuboring:</b>\n<i>Hozirgi dasturlash tili:</i><b> $langs[$lang]</b>",
                'parse_mode' => 'html'
            ]);
            exit();
        }
        if ($data == 'reinput'){
            $sql = "UPDATE telegram SET step=7 WHERE user_id='$chatId'";
            mysqli_query($conn, $sql);
            bot("sendMessage", [
                'chat_id' => $chatId,
                'text' => "<code>Kiruvchi ma'lumotni kiriting.</code>\n<i>ℹ️ Agar kiruvchi ma'lumot bo'lmasa: 0 ni yuboring</i>\n⏩ ",
                'parse_mode' => 'html'
            ]);
            exit();
        }
        if ($data == 'run'){
            
            bot("answerCallbackQuery", [
                'callback_query_id' => $callbackQueryId,
                'text' => "✅ Kodingiz tekshirilmoqda birozdan so'ng javobini olasiz :)",
                'show_alert' => true
            ]);     
            $res = compile($arr);
           
            if ($res->Result) $result = $res->Result;
            if (strlen($result) > 4096) $result = "❌ 4096 belgidan ko'p javoblarni yubora olmayman. Uzr!";
            $txt ="*❇️==== NATIJA ====❇️\n➖➖➖➖➖➖➖➖➖➖*\n`".$result."`\n➖➖➖➖➖➖➖➖➖➖";
            if ($res->Warnings) $txt.="\n🔻XATOLIK:\n`".$res->Warnings."`";
            if ($res->Errors) $txt.= "\n🔸LOG:\n`".$res->Errors."`";
            bot("editMessageText", [
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'text' => $txt,
                'parse_mode' => 'markdown'
            ]);
            exit();
        }
    }


    if ($update->inline_query){

        $inlineQuery = $update->inline_query;
        $queryId = $inlineQuery->id;
        $chatId = $inlineQuery->from->id;
        $qeuryResult = json_encode([
            [
                'type' => 'article',
                'id' => 0,
                'title' => "Kompilyatsiya qilish",
                'input_message_content' => [
                    'message_text' => "/run",
                ],
                'description' => "PHP, JAVA, JAVASCRIPT, MYSQL, C++, C#, PYTHON, PASCAL"
            ]
        ]);

        bot("answerInlineQuery", [
            'inline_query_id' => $queryId,
            'cache_time' => 0,
            'results' => $qeuryResult,
        ]);
        exit();
    }

    $message = $update->message;
    $text = $message->text;
    $chatId = $message->chat->id;
    $userId = $message->from->id;
	$chat_type = $update->message->chat->type;
    $reply = $update->message->reply_to_message->from->username;
    $sql = "INSERT IGNORE telegram SET user_id=$chatId";
    mysqli_query($conn, $sql);

    if  ($text != Null){
        $res = (mysqli_query($conn, "SELECT * FROM telegram WHERE user_id='$chatId'"))->fetch_array(MYSQLI_ASSOC);
        if ($res){
            $step = $res['step'];
            $lang = $res['lang'];
            $input = $res['input'];
            $code = $res['code'];
        }
    }

    if  ($text == "/start" || $text == "/start@webuzbot"){
        bot("sendMessage",[
            'chat_id' => $chatId,
            'text' => "Assalomu aleykum! Men <b>Compiler</b> botman.\nMen ishlay oladigan dastur tillari:<i>\n▫️Java\n▫️JavaScript\n▫️PHP\n▫️Pascal\n▫️Python\n▫️C++\n▫️MySQL</i>\n\n/help komandasi bilan foydalanish haqida ma'lumot olishingiz mumkin.\n /run - Kodni tekshirish\n /language - dasturlash tilini o'zgartirish\n\n<a href='https://t.me/baxa94'><i>Ismatov Bahodir</i></a>",
            'parse_mode' => 'html'
        ]);
        exit();
    }
    if ($text == "/language" || $text == "/language@webuzbot"){
        $inline = json_encode([
            'inline_keyboard' => [
                [['text' => 'Java', 'callback_data' => 4], ['text' => 'JavaScript', 'callback_data' => '17']],
                [['text' => 'PHP', 'callback_data' => 8], ['text' => 'Pascal', 'callback_data' => '9']],
                [['text' => 'C++', 'callback_data' => 27], ['text' => 'C#', 'callback_data' => '1']],
                [['text' => 'Python', 'callback_data' => 24], ['text' => 'MySQL', 'callback_data' => '33']]
            ]
        ]);
        bot("sendMessage", [
            'chat_id' => $chatId,
            'text' => "*Marhamat kerakli dasturlash tilini tanlang:*",
            'parse_mode' => 'markdown',
            'reply_markup' => $inline
        ]);
        exit();
    }
    if ($text == "/run" || $text == "/run@webuzbot"){

    	if ($lang == 1) {
    		$intro = "\n<code>public static void Main(string[] args){} siz yozing:</code>";
    	}

        bot("sendMessage", [
            'chat_id' => $chatId,
            'text' => "<b>Kompilyatsiya uchun kodingizni yuboring:</b>\n<i>Hozirgi dasturlash tili:</i><b> $langs[$lang]</b>$intro",
            'parse_mode' => 'html'
        ]);
        $sql = "UPDATE telegram SET step=1, code='', input='' WHERE user_id=$chatId";
        mysqli_query($conn, $sql);
        exit();
    }
    if ($text == "/help" || $text == "/help@webuzbot"){
        bot("sendVideo", [
            'chat_id'=>$chatId, 
            'video' => "https://igyazilim.000webhostapp.com/bot/demo.mp4",
            'caption'=>"<a href='https://telegra.ph/Compiler-Bot-uchun-qollanma-10-28'>Foydalanish haqida qo'llanma</a>",
            'parse_mode' => 'html'
        ]);
    }

	if (($reply == "webuzbot" && $chat_type != "private") || $chat_type == "private"){

	    if ($text != Null && $step == 1){
	        bot("sendMessage", [
	            'chat_id' => $chatId,
	            'text' => "✅ <b> Kodingiz qabul qilindi.</b>\n\n<code>Kiruvchi ma'lumotni kiriting.</code>\n<i>Agar kiruvchi ma'lumot bo'lmasa: 0 ni yuboring</i>\n\n⏩",
	            'parse_mode' => 'html'
	        ]);

	        $code = addslashes($text);
	        $sql = "UPDATE telegram SET step=2, code='$code' WHERE user_id=$chatId";
	        mysqli_query($conn, $sql);
	        exit();
	    }
	    if ($step == 2 && $text != Null){
	       
	        // $code = stripslashes($code);
	        $txt = "*Kodingiz:*\n➖➖➖➖➖➖➖➖➖➖\n`".$code."`\n➖➖➖➖➖➖➖➖➖➖\n*Kiruvchi ma'lumotlar:*\n`".$text."`";
	        $r=bot("sendMessage", [
	            'chat_id' => $chatId,
	            'text' => $txt,
	            'parse_mode' => 'markdown',
	            'reply_markup' => $inline_comp
	        ]);
	        $input = addslashes($text);
	        $sql = "UPDATE telegram SET step=3, input='$input' WHERE user_id='$chatId'";
	        mysqli_query($conn, $sql);
	        exit();
	    }
	    if ($step == 5 && $text != Null){
	        
	        $input = stripslashes($input);
	        bot("sendMessage", [
	            'chat_id' => $chatId,
	            'text' => "*Kodingiz:*\n➖➖➖➖➖➖➖➖➖➖\n`".$text."`\n➖➖➖➖➖➖➖➖➖➖\n*Kiruvchi ma'lumotlar:*\n`".$input."`",
	            'parse_mode' => 'markdown',
	            'reply_markup' => $inline_comp
	        ]);

	        $code = addslashes($text);
	        $sql = "UPDATE telegram SET step=3, code='$code' WHERE user_id='$chatId'";
	        mysqli_query($conn, $sql);
	        exit();
	    }
	    if ($step == 7 && $text != Null){
	        
	        $code = stripslashes($code);
	        bot("sendMessage", [
	            'chat_id' => $chatId,
	            'text' => "*Kodingiz:*\n➖➖➖➖➖➖➖➖➖➖\n`".$code."`\n➖➖➖➖➖➖➖➖➖➖\n*Kiruvchi ma'lumotlar:*\n`".$text."`",
	            'parse_mode' => 'markdown',
	            'reply_markup' => $inline_comp
	        ]);

	        $input = addslashes($text);
	        $sql = "UPDATE telegram SET step=3, input='$input' WHERE user_id='$chatId'";
	        mysqli_query($conn, $sql);
	        exit();
	    }
	}
}

// echo bot("setwebhook", ['url'=>'WEBHOOK ADDRESS']);

?>
