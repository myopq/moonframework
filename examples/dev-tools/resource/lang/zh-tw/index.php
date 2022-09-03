<?php

return [
    'site_title' => '一個自動編解碼:Json, Url, Base64, 時間戳(Timestamp)的開發者在線工具 - Develop Tools',
    'site_description' => '工具會自動識別輸入的內容，並進行編碼或解碼，目前支持: JSON、URL編碼解碼、時間戳解碼、Base64解碼，更多功能正在開發中，歡迎貢獻代碼。 ',
    'language' => '選擇語言',
    'welcome' => '歡迎使用自動編/解碼工具<br />
    在這個工具中，你可以直接輸入 <kbd>JSON</kbd> <kbd>時間戳(TimeStamp)</kbd>, <kbd>網址(URL)</kbd>, 該工具會自動檢測輸入內容的類型，並進行編碼或解碼。 ',
    'more_function' => '更多功能開發中。 ',
    'current_ts' => '當前時間戳',
    'current_dt' => '當前時間',
    'input_hint' => '請輸入JSON、URL、TimeStamp...',
    'result' => '結果',

    // timestamp parser
    'several_format_outputs' => '不同格式的日期輸出:',
    'Format' => '格式',
    'Value' => '值',
    'ISO' => 'ISO',
    'Local_DateTime' => '本地時間',
    'GMT_DateTime' => 'GMT標準時間',

    // json parser
    'Copy' => '複製',
    'Collapse' => '折疊',
    'Collapse_Dropdown' => '折疊',
    'Collapse_2' => '折疊第2層',
    'Collapse_3' => '折疊第3層',
    'Expand' => '展開',

    // section timestamp
    'tsp_head' => '在線TimeStamp轉換工具',
    'tsp_desc' => '自動實現timestamp和時間字符串的轉換，請在上面輸入要轉換的字符串。 ',
    'tsp_get_in_prog' => '在不同的開發語言中實現獲取當前timestamp的方法',
    'tsp_in_swift' => '獲取當頭timestamp在swift',
    'tsp_in_golang' => '獲取當頭timestamp在Go lang',
    'tsp_in_java' => '獲取當頭timestamp在Java',
    'tsp_in_javascript' => '獲取當頭timestamp在JavaScript',
    'tsp_in_oc' => '獲取當頭timestamp在Objective-C',
    'tsp_in_mysql' => '獲取當頭timestamp在MySQL',
    'tsp_in_sqllite' => '獲取當頭timestamp在SQLite',
    'tsp_in_erlang' => '獲取當頭timestamp在Erlang',
    'tsp_in_php' => '獲取當頭timestamp在PHP',
    'tsp_in_py' => '獲取當頭timestamp在Python',
    'tsp_in_puby' => '獲取當頭timestamp在Ruby',
    'tsp_in_shell' => '獲取當頭timestamp在Shell',
    'tsp_in_groovy' => '獲取當頭timestamp在Groovy',
    'tsp_in_lua' => '獲取當頭timestamp在Lua',
    'tsp_in_csharp' => '獲取當頭timestamp在.NET/C#',

    // section url
    'url_head' => 'URL 解碼(decode) / URL 編碼(encode)',
    'url_desc1' => '一個在線的url編碼解碼工具.',
    'url_desc2' => 'URL 是網頁的地址，如：https://dev-tools.link，URL 編碼將字符轉換為可以在 Internet 上傳輸的格式.',
    'url_desc3' => 'URL 編碼表示通過將 URL 中的某些字符替換為一個或多個由百分比字符“%”後跟兩個十六進制數字組成的字符來對 URL 中的某些字符進行編碼，那兩個十六進制數字是被替換的字符。 ',
    'url_desc4' => '分析一個URL，他們的基礎格式是：: <br />scheme:[//[user:password@]host[:port]][/]path[?query][#fragment]<br /><br />對 URI 進行編碼的第一步是檢查其部分，然後僅對相關部分進行編碼.',
    'url_in_js' => 'javascript url encode / javascript url decode',
    'url_in_php' => 'php urlencode / php urldecode',
    'url_in_py' => 'python urlencode / python urldecode',
    'url_in_java' => 'java url encode / java url decode',
    
    // section json
    'json_head' => 'JSON格式化',
    'json_desc1' => 'JSON 格式在語法上與創建 JavaScript 對象的代碼相同。由於這種相似性，JavaScript 程序可以輕鬆地將 JSON 數據轉換為原生 JavaScript 對象。 JSON 語法源自 JavaScript 對象表示法語法，但 JSON 格式僅為文本。 ',
    'json_desc2' => '免費的 JSON 美化工具，用作 JSON 編輯器、Json 查看器、Json驗證、程序，以在樹視圖和純文本中解析 json.',
    'json_in_js' => 'javascript json parse',
    'json_in_py_dump' => 'python json dump',
    'json_in_py_load' => 'python json loads &amp; python parse json',
    'json_in_py_dict2json' => 'python dict to json',
    'json_in_json' => 'java parse json',
    'json_in_php' => 'php json encode/decode',
    'json_in_js_obj2json' => 'javascript object to json',
];