<?php
    assert_options(ASSERT_ACTIVE, 1);
    assert_options(ASSERT_WARNING, 0);
    assert_options(ASSERT_QUIET_EVAL, 1);
    assert_options(ASSERT_CALLBACK, function($file, $line, $code, $desc = null){
        echo json_encode(func_get_args());
    });

    assert(false);
    assert("2>1", "succ");
    die;


    $url = 'https://sandbox.itunes.apple.com/verifyReceipt';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_CAINFO, "dev.cer");
    $res = curl_exec($ch);
    print_r(curl_getinfo($ch));
    if ($res === false) {
        echo "errcode:" . curl_errno($ch) . "\n";
        echo "errmsg:" . curl_error($ch) . "\n";
    } else {
        echo "succ:" . "\n";
    }
    curl_close($ch);
