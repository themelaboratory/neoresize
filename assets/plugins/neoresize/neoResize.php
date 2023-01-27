<?php
/**
 * Class directResize
 */

class directResize
{

    /**
     * directResize constructor.
     * @param $config
     */
    function __construct($config)
    {
        $this->SetConfig($config);
    }


    /**
     * @param $config
     */
    function SetConfig($config)
    {
        global $modx;

        if (file_exists($modx->config["base_path"] . DIRECTRESIZE_PATH . "configs/$config.config.php"))
            @ include_once $modx->config["base_path"] . DIRECTRESIZE_PATH . "configs/$config.config.php";

        $local_parms = array(
            "tpl" => '<a href="[+dr.big.src+]" target="_blank">[+dr.shortcut.tpl_thumb+]</a>',
        );

        foreach ($local_parms as $key => $val) {
            if (isset ($dr_local[$key])) {
                $array_val = is_array($dr_local[$key]) ? $dr_local[$key] : array(
                    "all" => $dr_local[$key]
                );
            } else {
                $array_val = array(
                    "all" => $val
                );
            }
            $this->drconfig[$key] = array_merge(array(
                "all" => $val
            ), $array_val);
        }

        $global_parms = array(
            "path" => "assets/images",
            "bindings_priority" => "label,css,template,path"
        );

        foreach ($global_parms as $key => $val) {
            $this->drglobal[$key] = isset ($dr_global[$key]) ? $dr_global[$key] : $val;
        }
    }


    /**
     * @return array|false
     */
    function OriginalImgSize()
    {

        $p = $this->absolutePath($this->current["source"]["src"]);
        if($p){
           $file = getimagesize($p);
           $file["width"] = $file[0];
           $file["height"] = $file[1];
       }else {
           $file = [];
       }

        return $file;
    }

    /**
     * @return false|mixed|string
     */
    function checkPath()
    {
        $ap = $this->absolutePath($this->current["source"]["src"]);

        if (isset($ap) && $ap != '' && !@ getimagesize($ap)) {
            return false;
        }

        $rules = str_replace("\r", "", $this->drglobal["path"]);
        $rules = explode("\n", $rules);

        foreach ($rules as $val) {
            if (strlen(trim($val)) > 0) {
                if (substr($val, 0, 1) == "!") {
                    $tmp = $this->absolutePath(substr($val, 1, strlen($val)));
                    if ($tmp)
                        $rule["deny"][] = $tmp;
                } else {
                    $tmp = $this->absolutePath($val);
                    if ($tmp)
                        $rule["allow"][] = $tmp;
                }
            }
        }

        $path = $this->absolutePath(dirname($this->current["source"]["src"]));

        $longest_deny_path = "";
        if (is_array($rule["deny"]))
            foreach ($rule["deny"] as $r) {
                if (substr($path, 0, strlen($r)) == $r && strlen($r) > strlen($longest_deny_path))
                    $longest_deny_path = $r;
            }

        $longest_allow_path = "";
        if (is_array($rule["allow"]))
            foreach ($rule["allow"] as $r) {
                if (substr($path, 0, strlen($r)) == $r && strlen($r) > strlen($longest_allow_path)) {
                    $longest_allow_path = $r;
                }
            }

        $result = strlen($longest_deny_path) > strlen($longest_allow_path) ? false : $longest_allow_path;

        return $result;
    }


    /**
     * @param $path
     * @return string|string[]
     */
    function _removeSiteUrl($path)
    {
        global $modx;
        $path = str_replace($modx->config["site_url"], "", $path);
        return $path;
    }

    /**
     * @param $path
     * @return false|string|string[]
     */
    function absolutePath($path)
    {
        global $modx;

        $path = $this->_removeSiteUrl($path);

        // If we gave encoded empry spaces in filename
        $path = str_replace('%20', ' ', $path);

        if (strstr($path, "://")){
            return $path;
        }

        $basePath = dirname($_SERVER['SCRIPT_FILENAME']);

        $absPath = realpath("{$basePath}/{$path}");
        if (substr($absPath, strlen($absPath) - 1, 1) == "/" || substr($absPath, strlen($absPath) - 1, 1) == "\\") {
            $absPath = substr($absPath, 0, strlen($absPath) - 1);
        }

        return $absPath;
    }


    /**
     * @return array
     */
    function GetCurrentConfig()
    {
        return $this->_getCurrentConfigParms();
    }


    /**
     * @param string $config_name
     * @return array
     */
    function _getCurrentConfigParms($config_name = "all")
    {
        if (is_array($this->used_configs[$config_name])) {
            $current_config = $this->used_configs[$config_name];
        } else {
            foreach ($this->drconfig as $key => $val) {
                $current_config[$key] = $this->drconfig[$key][$config_name] ? $this->drconfig[$key][$config_name] : $this->drconfig[$key]["all"];
            }
            $this->used_configs[$config_name] = $current_config;
        }

        return $current_config;
    }


    /**
     * @return array
     */
    function _getSourceImgAttr()
    {
        preg_match_all("/(src|height|width|alt|title|class|valign|align|style|hspace|vspace|border)\s*=\s*(['\"])(.*?)\\2/i", $this->current["img_tag"], $match);
        $result = array_combine($match[1], $match[3]);

        if ($result["style"]) {
            preg_match_all("/(width|height)\s*:\s*([0-9]+)px/i", $result["style"], $match);
            if (!empty ($match[1]))
                $result_style = array_combine($match[1], $match[2]);
            if (is_array($result_style))
                $result = array_merge($result, $result_style);
        }

        return $result;
    }


    /**
     * @return false|mixed|string
     */
    function _canProcessImage()
    {
        $this->current["longest_allowed_path"] = $this->checkPath();
        return $this->current["longest_allowed_path"];
    }


    /**
     * @return bool
     */
    function differentSize()
    {
        $check = ($this->current['file']['width'] != $this->current['source']['width'] || $this->current['file']['height'] != $this->current['source']['height'])
            && (!empty($this->current['source']['width']) || !empty($this->current['source']['height']));
        return $check;
    }


    /**
     * @return false|string
     */
    function onServer()
    {
        $check = strstr($this->current['source']['src'], $this->drglobal['path']);
        return $check;
    }


    /**
     * @param $content
     * @return mixed|string|string[]
     */
    function Process($content)
    {
        global $modx;

        preg_match_all("/<img[^>]*>/", $content, $imgs);
        $imgs_all = $imgs[0];

        for ($n = 0; $n < count($imgs_all); $n++) {

            $this->current = [];
            $this->current["config"] = $this->GetCurrentConfig();

            $this->current["img_tag"] = $imgs_all[$n];
            $this->current["source"] = $this->_getSourceImgAttr();
            if ($this->onServer()) {
                if ($this->_canProcessImage()) {
                    $this->current["file"] = $this->OriginalImgSize();
                    if ($this->differentSize()) {
                        $new_tpl = $this->ParseTemplate($this->current["config"]["tpl"]);
                        $content = str_replace($this->current["img_tag"], $new_tpl, $content);
                    }
                }
            }
        }

        return $content;
    }


    /**
     * @param $tpl
     * @return mixed|string|string[]
     */
    function ParseTemplate($tpl)
    {
        global $modx;

        if (!class_exists('DRChunkie')) {
            require_once($modx->config["base_path"] . DIRECTRESIZE_PATH . "includes/chunkie.class.inc.php");
        }
        $drtemplate = new DRChunkie($tpl);

        $context = array();
        $context["source"] = $this->current["source"];

        $drtemplate->addVar('dr', $context);
        return $drtemplate->Render();
    }
}
?>
