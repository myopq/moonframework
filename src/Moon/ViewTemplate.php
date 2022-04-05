<?php
declare(strict_types=1);

namespace Moon;

use Moon\Exception\HttpException;
use Moon\MoonException;
use Moon\Facades\C;

class ViewTemplate {
	private $subtemplates = array();
	private $cssModules = '';
	private $jsModules = '';
	private $replacecode = array('search' => array(), 'replace' => array());
	private $blocks = array();
	private $language = array();
	private $file = '';

    private $themes = "default";
    private $cacheDir = "";


    function __construct()
    {
        $this->themes = C::get('view/themes');
        $this->cacheDir = template_cache_path($this->themes);
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

	function parse_template($tplfile, $cachefile) {
		$this->file = $tplfile;
		if($fp = @fopen($tplfile, 'r')) {
			$template = @fread($fp, filesize($tplfile));
			fclose($fp);
		} else {
			exit('tpl no exists.');
		}

		$var_regexp = "((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(\-\>)?[a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)";
		$const_regexp = "([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)";

		$this->subtemplates = [];
		for($i = 1; $i <= 5; $i++) {
			if(strpos($template, '{subtemplate') !== false) {
				$template = preg_replace_callback("/[\n\r\t]*(\<\!\-\-)?\{subtemplate\s+([a-z0-9_:\/]+)\}(\-\-\>)?[\n\r\t]*/is", function($mts) {
					return $this->loadsubtemplate("{$mts[2]}");
				}, $template);
			}
		}

		$this->cssModules = [];
		$count = 0;
		while (strpos($template, '{assets ') !== false && $count++ < 10) {
			$template = preg_replace_callback("/[\n\r\t]*(\<\!\-\-)?\{assets\s+([a-z0-9_:\/\.\-]+)\}(\-\-\>)?[\n\r\t]*/is", function($matches) {
				return $this->loadAssets($matches[2]);
			}, $template);
		}

		$template = preg_replace("/([\n\r]+)\t+/s", "\\1", $template);
		$template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
		$template = preg_replace_callback("/\{lang\s+(.+?)\}/is", function($mts) {
			return $this->languagevar($mts[1]);
		}, $template);
		// $template = preg_replace("/[\n\r\t]*\{block\/(\d+?)\}[\n\r\t]*/ie", "\$this->blocktags('\\1')", $template);
		// $template = preg_replace("/[\n\r\t]*\{blockdata\/(\d+?)\}[\n\r\t]*/ie", "\$this->blockdatatags('\\1')", $template);
		// $template = preg_replace("/[\n\r\t]*\{ad\/(.+?)\}[\n\r\t]*/ie", "\$this->adtags('\\1')", $template);
		// $template = preg_replace("/[\n\r\t]*\{ad\s+([a-zA-Z0-9_\[\]]+)\/(.+?)\}[\n\r\t]*/ie", "\$this->adtags('\\2', '\\1')", $template);
		$template = preg_replace_callback("/[\n\r\t]*\{date\((.+?)\)\}[\n\r\t]*/i", function($mts) {
			return self::datetags($mts[1]);
		}, $template);
		$template = preg_replace_callback("/[\n\r\t]*\{safehtml\((.+?)\)\}[\n\r\t]*/i", function($mts) {
			return self::safehtmltags($mts[1]);
		}, $template);
		// $template = preg_replace("/[\n\r\t]*\{avatar\((.+?)\)\}[\n\r\t]*/ie", "\$this->avatartags('\\1')", $template);
		$template = preg_replace_callback("/[\n\r\t]*\{eval\}\s*(\<\!\-\-)*(.+?)(\-\-\>)*\s*\{\/eval\}[\n\r\t]*/is", function($mts) {
			return $this->evaltags($mts[2]);
		}, $template);
		$template = preg_replace_callback("/[\n\r\t]*\{eval\s+(.+?)\s*\}[\n\r\t]*/is", function($mts) {
			return $this->evaltags($mts[1]);
		}, $template);
		$template = preg_replace_callback("/[\n\r\t]*\{html\s+(.+?)\s*\}[\n\r\t]*/is", function($mts) {
			return $this->evaltags("echo html_" . $mts[1] . ";");
		}, $template);
		// $template = preg_replace("/[\n\r\t]*\{csstemplate\}[\n\r\t]*/ies", "\$this->loadcsstemplate()", $template);
		$template = str_replace("{LF}", "<?=\"\\n\"?>", $template);
		$template = preg_replace("/\{(\\\$[a-zA-Z0-9_\-\>\[\]\'\"\$\.\x7f-\xff]+)\}/s", "<?=\\1?>", $template);
		// $template = preg_replace("/\{hook\/(\w+?)(\s+(.+?))?\}/ie", "\$this->hooktags('\\1', '\\3')", $template);
		
		// 只处理{}包起来的，不处理$这样的
		/*
		$template = preg_replace_callback("/$var_regexp/s", function($mts) {
			return template::addquote("<?={$mts[1]}?>");
		}, $template);
		*/
		
		$template = preg_replace_callback("/\<\?\=\<\?\=$var_regexp\?\>\?\>/s", function($mts) {
			return $this->addquote("<?={$mts[1]}?>");
		}, $template);
		

		$headeradd = '';
		if(!empty($this->subtemplates)) {
			$headeradd .= "\n0\n";
			foreach($this->subtemplates as $fname) {
				// $headeradd .= "|| checktplrefresh('$tplfile', '$fname', ".time().", '$cachefile')\n";
				$tpl_path = resource_path("view/" . $this->themes) . '/' . $fname . '.htm';
				$headeradd .= "|| checktplrefresh('$tplfile', '$tpl_path', ".time().", '$cachefile')\n";
			}
			$headeradd .= ';';
		}

		if(!empty($this->blocks)) {
			$headeradd .= "\n";
			$headeradd .= "block_get('".implode(',', $this->blocks)."');";
		}

		$template = "<? if(!defined('MOON_PATH')) exit('Access Denied'); {$headeradd}?>\n$template";

		$template = preg_replace_callback("/[\n\r\t]*\{template\s+([a-z0-9_:\/]+)\}[\n\r\t]*/is", function($mts) {
			return $this->stripvtags('<? include template(\''.$mts[1].'\'); ?>');
		}, $template);
		$template = preg_replace_callback("/[\n\r\t]*\{template\s+(.+?)\}[\n\r\t]*/is", function($mts) {
			return $this->stripvtags('<? include template(\''.$mts[1].'\'); ?>');
		}, $template);
		$template = preg_replace_callback("/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/is", function($mts) {
			return $this->stripvtags('<? echo '.$mts[1].'; ?>');
		}, $template);
		$template = preg_replace_callback("/[\n\r\t]*\{\=(.+?)\}[\n\r\t]*/is", function($mts) {
			return $this->stripvtags('<? echo '.$mts[1].'; ?>');
		}, $template);
		$template = preg_replace_callback("/([\n\r\t]*)\{if\s+(.+?)\}([\n\r\t]*)/is", function($mts) {
			return $this->stripvtags($mts[1].'<? if('.$mts[2].') { ?>'.$mts[3]);
		}, $template);
		$template = preg_replace_callback("/([\n\r\t]*)\{elseif\s+(.+?)\}([\n\r\t]*)/is", function($mts) {
			return $this->stripvtags($mts[1].'<? } elseif('.$mts[2].') { ?>'.$mts[3]);
		}, $template);
		$template = preg_replace("/\{else\}/i", "<? } else { ?>", $template);
		$template = preg_replace("/\{\/if\}/i", "<? } ?>", $template);

		$template = preg_replace_callback("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r\t]*/is", function($mts) {
			return $this->stripvtags('<? if(is_array('.$mts[1].')) foreach('.$mts[1].' as '.$mts[2].') { ?>');
		}, $template);
		$template = preg_replace_callback("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*/is", function($mts) {
			return $this->stripvtags('<? if(is_array('.$mts[1].')) foreach('.$mts[1].' as '.$mts[2].' => '.$mts[3].') { ?>');
		}, $template);
		$template = preg_replace("/\{\/loop\}/i", "<? } ?>", $template);
		// 形如{{}}的不处理
		$template = preg_replace("/[^\{]\{$const_regexp\}/s", "<?=\\1?>", $template);
		if(!empty($this->replacecode)) {
			$template = str_replace($this->replacecode['search'], $this->replacecode['replace'], $template);
		}
		$template = preg_replace("/ \?\>[\n\r]*\<\? /s", " ", $template);

		if(!is_dir(dirname($cachefile))) {
			mkdir(dirname($cachefile), 0777,true);
		}
		if(!@$fp = fopen($cachefile, 'w')) {
			$this->error('directory_notfound', dirname($cachefile));
		}

		$template = preg_replace_callback("/\"(http)?[\w\.\/:]+\?[^\"]+?&[^\"]+?\"/", function($mts) {
			return $this->transamp("{$mts[0]}");
		}, $template);
		/*
		$template = preg_replace_callback("/\<script[^\>]*?src=\"(.+?)\"(.*?)\>\s*\<\/script\>/is", function($mts) {
			return $this->stripscriptamp("{$mts[1]}", "{$mts[2]}");
		}, $template);
		*/
		$template = preg_replace_callback("/[\n\r\t]*\{block\s+([a-zA-Z0-9_\[\]]+)\}(.+?)\{\/block\}/is", function($mts) {
			return $this->stripblock("{$mts[1]}", "{$mts[2]}");
		}, $template);
		$template = preg_replace("/\<\?(\s{1})/is", "<?php\\1", $template);
		$template = preg_replace("/\<\?\=(.+?)\?\>/is", "<?php echo \\1;?>", $template);

		//模板特殊标识替换
		$tmpl_parse_string = C::get('view/vars');
		if($tmpl_parse_string) {
			foreach ($tmpl_parse_string as $key => $value) {
				$template = str_replace($key, $value, $template);
			}
		}

		flock($fp, 2);
		fwrite($fp, $template);
		fclose($fp);
	}

	function languagevar($var) {
		$vars = explode(':', $var);

        !isset($this->language['inner']) && $this->language['inner'] = array();
        $langvar = &$this->language['inner'];
		
		if(isset($langvar[$var])) {
			return $langvar[$var];
		} else {
			return '!'.$var.'!';
		}
	}

	function blocktags($parameter) {
		$bid = intval(trim($parameter));
		$this->blocks[] = $bid;
		$i = count($this->replacecode['search']);
		$this->replacecode['search'][$i] = $search = "<!--BLOCK_TAG_$i-->";
		$this->replacecode['replace'][$i] = "<?php block_display('$bid');?>";
		return $search;
	}

	function blockdatatags($parameter) {
		$bid = intval(trim($parameter));
		$this->blocks[] = $bid;
		$i = count($this->replacecode['search']);
		$this->replacecode['search'][$i] = $search = "<!--BLOCKDATA_TAG_$i-->";
		$this->replacecode['replace'][$i] = "";
		return $search;
	}

	function adtags($parameter, $varname = '') {
		$parameter = stripslashes($parameter);
		$i = count($this->replacecode['search']);
		$this->replacecode['search'][$i] = $search = "<!--AD_TAG_$i-->";
		$this->replacecode['replace'][$i] = "<?php ".(!$varname ? 'echo ' : '$'.$varname.'=')."adshow(\"$parameter\");?>";
		return $search;
	}

	function datetags($parameter) {
		$parameter = stripslashes($parameter);
		$i = count($this->replacecode['search']);
		$this->replacecode['search'][$i] = $search = "<!--DATE_TAG_$i-->";
		$this->replacecode['replace'][$i] = "<?php echo dgmdate($parameter);?>";
		return $search;
	}

	function avatartags($parameter) {
		$parameter = stripslashes($parameter);
		$i = count($this->replacecode['search']);
		$this->replacecode['search'][$i] = $search = "<!--AVATAR_TAG_$i-->";
		$this->replacecode['replace'][$i] = "<?php echo avatar($parameter);?>";
		return $search;
	}

	function evaltags($php) {
		$php = str_replace('\"', '"', $php);
		$i = count($this->replacecode['search']);
		$this->replacecode['search'][$i] = $search = "<!--EVAL_TAG_$i-->";
		$this->replacecode['replace'][$i] = "<? $php?>";
		return $search;
	}

	function safehtmltags($parameter) {
		$parameter = stripslashes($parameter);
		$i = count($this->replacecode['search']);
		$this->replacecode['search'][$i] = $search = "<!--SAFEHTML_TAG_$i-->";
		$this->replacecode['replace'][$i] = "<?php echo safehtml($parameter);?>";
		return $search;
	}

	function hooktags($hookid, $key = '') {
		global $_G;
		$i = count($this->replacecode['search']);
		$this->replacecode['search'][$i] = $search = "<!--HOOK_TAG_$i-->";
		$dev = '';
		if(isset($_G['config']['plugindeveloper']) && $_G['config']['plugindeveloper'] == 2) {
			$dev = "echo '<hook>[".($key ? 'array' : 'string')." $hookid".($key ? '/\'.'.$key.'.\'' : '')."]</hook>';";
		}
		$key = $key !== '' ? "[$key]" : '';
		$this->replacecode['replace'][$i] = "<?php {$dev}if(!empty(\$_G['setting']['pluginhooks']['$hookid']$key)) echo \$_G['setting']['pluginhooks']['$hookid']$key;?>";
		return $search;
	}

	function stripphpcode($type, $code) {
		$this->phpcode[$type][] = $code;
		return '{phpcode:'.$type.'/'.(count($this->phpcode[$type]) - 1).'}';
	}

	function loadsubtemplate($file) {
		$tplfile = template($file);
		if($content = @implode('', file($tplfile))) {
			$this->subtemplates[] = $file;
			return $content;
		} else {
			return '<!-- '.$file.' -->';
		}
	}

	function loadAssets($file) {
		$realFile = public_path($file);
		if (!is_file($realFile)) {
			throw new HttpException("{$realFile} not exists.");
		}

		$hash = hash_file("md5", $realFile);

		$link = '//' . C::get("http/host") . '/' . $file . "?" . $hash;
		return $link;
	}


	function getphptemplate($content) {
		$pos = strpos($content, "\n");
		return $pos !== false ? substr($content, $pos + 1) : $content;
	}

	function cssvtags($param, $content) {
		return;
	}

	function transamp($str) {
		// $str = str_replace('&', '&amp;', $str);
		$str = str_replace('&amp;amp;', '&amp;', $str);
		$str = str_replace('\"', '"', $str);
		return $str;
	}

	function addquote($var) {
		return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
	}


	function stripvtags($expr, $statement = '') {
		$expr = str_replace("\\\"", "\"", preg_replace("/\<\?\=(\\\$.+?)\?\>/s", "\\1", $expr));
		$statement = str_replace("\\\"", "\"", $statement);
		return $expr.$statement;
	}

	function stripscriptamp($s, $extra) {
		$extra = str_replace('\\"', '"', $extra);
		$s = str_replace('&amp;', '&', $s);
		return "<script src=\"$s\" type=\"text/javascript\"$extra></script>";
	}

	function stripblock($var, $s) {
		$s = str_replace('\\"', '"', $s);
		$s = preg_replace("/<\?=\\\$(.+?)\?>/", "{\$\\1}", $s);
		preg_match_all("/<\?=(.+?)\?>/e", $s, $constary);
		$constadd = '';
		$constary[1] = array_unique($constary[1]);
		foreach($constary[1] as $const) {
			$constadd .= '$__'.$const.' = '.$const.';';
		}
		$s = preg_replace("/<\?=(.+?)\?>/", "{\$__\\1}", $s);
		$s = str_replace('?>', "\n\$$var .= <<<EOF\n", $s);
		$s = str_replace('<?', "\nEOF;\n", $s);
		$s = str_replace("\nphp ", "\n", $s);
		return "<?\n$constadd\$$var = <<<EOF\n".$s."\nEOF;\n?>";
	}

	function error($message, $tplname) {
        throw new MoonException("{$message} : {$tplname}.");
	}
}