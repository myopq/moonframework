<?php

return [
    'site_title' => '一个自动编解码:Json, Url, Base64, 时间戳(Timestamp)的开发者在线工具 - Develop Tools',
    'site_description' => '工具会自动识别输入的内容，并进行编码或解码，目前支持: JSON、URL编码解码、时间戳解码、Base64解码，更多功能正在开发中，欢迎贡献代码。',
    'language' => '选择语言',
    'welcome' => '欢迎使用自动编/解码工具<br />
    在这个工具中，你可以直接输入 <kbd>JSON</kbd> <kbd>时间戳(TimeStamp)</kbd>, <kbd>网址(URL)</kbd>, 该工具会自动检测输入内容的类型，并进行编码或解码。',
    'more_function' => '更多功能开发中。',
    'current_ts' => '当前时间戳',
    'current_dt' => '当前时间',
    'input_hint' => '请输入JSON、URL、TimeStamp...',
    'result' => '结果',

    // timestamp parser
    'several_format_outputs' => '不同格式的日期输出:',
    'Format' => '格式',
    'Value' => '值',
    'ISO' => 'ISO',
    'Local_DateTime' => '本地时间',
    'GMT_DateTime' => 'GMT标准时间',

    // json parser
    'Copy' => '复制',
    'Collapse' => '折叠',
    'Collapse_Dropdown' => '折叠',
    'Collapse_2' => '折叠第2层',
    'Collapse_3' => '折叠第3层',
    'Expand' => '展开',

    // section timestamp
    'tsp_head' => '在线TimeStamp转换工具',
    'tsp_desc' => '自动实现timestamp和时间字符串的转换，请在上面输入要转换的字符串。',
    'tsp_get_in_prog' => '在不同的开发语言中实现获取当前timestamp的方法',
    'tsp_in_swift' => '获取当头timestamp在swift',
    'tsp_in_golang' => '获取当头timestamp在Go lang',
    'tsp_in_java' => '获取当头timestamp在Java',
    'tsp_in_javascript' => '获取当头timestamp在JavaScript',
    'tsp_in_oc' => '获取当头timestamp在Objective-C',
    'tsp_in_mysql' => '获取当头timestamp在MySQL',
    'tsp_in_sqllite' => '获取当头timestamp在SQLite',
    'tsp_in_erlang' => '获取当头timestamp在Erlang',
    'tsp_in_php' => '获取当头timestamp在PHP',
    'tsp_in_py' => '获取当头timestamp在Python',
    'tsp_in_puby' => '获取当头timestamp在Ruby',
    'tsp_in_shell' => '获取当头timestamp在Shell',
    'tsp_in_groovy' => '获取当头timestamp在Groovy',
    'tsp_in_lua' => '获取当头timestamp在Lua',
    'tsp_in_csharp' => '获取当头timestamp在.NET/C#',

    // section url
    'url_head' => 'URL 解码(decode) / URL 编码(encode)',
    'url_desc1' => '一个在线的url编码解码工具.',
    'url_desc2' => 'URL 是网页的地址，如：https://dev-tools.link，URL 编码将字符转换为可以在 Internet 上传输的格式.',
    'url_desc3' => 'URL 编码表示通过将 URL 中的某些字符替换为一个或多个由百分比字符“%”后跟两个十六进制数字组成的字符来对 URL 中的某些字符进行编码，那两个十六进制数字是被替换的字符。',
    'url_desc4' => '分析一个URL，他们的基础格式是：: <br />scheme:[//[user:password@]host[:port]][/]path[?query][#fragment]<br /><br />对 URI 进行编码的第一步是检查其部分，然后仅对相关部分进行编码.',
    'url_in_js' => 'javascript url encode / javascript url decode',
    'url_in_php' => 'php urlencode / php urldecode',
    'url_in_py' => 'python urlencode / python urldecode',
    'url_in_java' => 'java url encode / java url decode',
    
    // section json
    'json_head' => 'JSON格式化',
    'json_desc1' => 'JSON 格式在语法上与创建 JavaScript 对象的代码相同。 由于这种相似性，JavaScript 程序可以轻松地将 JSON 数据转换为原生 JavaScript 对象。 JSON 语法源自 JavaScript 对象表示法语法，但 JSON 格式仅为文本。',
    'json_desc2' => '免费的 JSON 美化工具，用作 JSON 编辑器、Json 查看器、Json验证、程序，以在树视图和纯文本中解析 json.',
    'json_in_js' => 'javascript json parse',
    'json_in_py_dump' => 'python json dump',
    'json_in_py_load' => 'python json loads &amp; python parse json',
    'json_in_py_dict2json' => 'python dict to json',
    'json_in_json' => 'java parse json',
    'json_in_php' => 'php json encode/decode',
    'json_in_js_obj2json' => 'javascript object to json',
];