<?php
/*
$Date: 2012-09-07 21:10:34 +0800 $
$Author: enj0y $
*/

define(OSSWP_VERSION,'1.5');

require_once(dirname(__FILE__).'/class-plugin-public.php');
require_once(dirname(__FILE__).'/sdk.class.php');
class Enj0yWordPressossPlugin extends Enj0yWordPressossPluginPublic {
    function Enj0yWordPressossPlugin() {
		parent::Enj0yWordPressossPluginPublic();
		if (!file_exists(dirname(__FILE__).'/config.php')) add_action('admin_menu', array(&$this, 'settings'));
        if (!$this->options['hidealiyunossUploadTab']) {
			add_action('load-upload.php', array(&$this, 'addPhotosTab')); // WP < 2.5
			
			// WP >= 2.5
			add_action('media_buttons_context', array(&$this, 'media_buttons')); 
			add_action('media_upload_Enj0y-wordpress-oss', array(&$this, 'media_upload_content'));
		}
        add_action('activate_Enj0y/wordpress-oss.php', array(&$this, 'activate'));
        if ($_GET['Enj0yActivate'] == 'wordpress-oss') {
            $this->showConfigNotice();
        }
        $this->photos = array();
        $this->albums = array();
        $this->perPage = 1000;
		
		add_action('wp_delete_file', array(__CLASS__, 'wp_delete_file'));


    }
    function installAjax() {
        $js = array('ossAjax.js');
    }
    
    function activate() {
        wp_redirect('plugins.php?Activate=oss4wp');
        exit;
    }
    function deactivate() {}
    
    function showConfigNotice() {
        add_action('admin_notices', create_function('', 'echo \'<div id="message" class="updated fade"><p>Aliyun oss Plugin for WordPress <strong>activated</strong>. <a href="options-general.php?page=oss4wp/wordpress-oss/class-plugin.php">Configure the plugin &gt;</a></p></div>\';'));
    }

	function settings() {
		add_options_page('oss4wp', 'oss4wp', 10, __FILE__, array(&$this, 'admin'));
		$this->version_check();
	}
    function addhooks() {
		parent::addhooks();
        if (!$_POST['disable_aliyunoss']) {
            add_filter('wp_update_attachment_metadata', array(&$this, 'wp_update_attachment_metadata'), 9, 2);
        }
    }  
    function version_check() {
        global $Enj0yVersionCheck;
        if (is_object($Enj0yVersionCheck)) {
            $data = get_plugin_data(dirname(__FILE__).'/../wordpress-oss.php');
            $Enj0yVersionCheck->versionCheck(668, $data['Version']);
        }
    }
    function admin() {
        if ($_POST['action'] == 'save') {
            if (!is_array($_POST['options'])) $_POST['options'] = array();
            $options = get_option('enj0y_wordpress_oss');            
            $_POST['options']['key'] = trim($_POST['options']['key']);
            $_POST['options']['secret'] = trim($_POST['options']['secret']);

			if($_POST['options']['expires'])$_POST['options']['expires'] = intval($_POST['options']['expires']);

            if (!$_POST['options']['secret'] || ereg('\*\*\*\*', $_POST['options']['secret'])) {
                $_POST['options']['secret'] = $options['secret'];
            }
            if($_POST['options']['bucket']=='x'&&$_POST['newbucket']){
				//新建BUCKET
				$_POST['options']['bucket']=$bucketname=@htmlspecialchars(trim($_POST['newbucket']));
				if(strlen($bucketname)<3||strlen($bucketname)>32)$error = "Bucket名称只能是3到32位的字母或数字组成~. ";
				else{
					$obj = new ALIOSS($_POST['options']['key'],$_POST['options']['secret']);
					$obj->set_debug_mode(TRUE);
					$s=$obj->create_bucket($bucketname, 'public-read');
					$errcode=$obj->errcode;
					if($errcode=="BucketAlreadyExists")$error = "创建失败，原因：Bucket已被占用~. ";
					elseif($errcode=="TooManyBuckets")$error = "创建失败，原因：Bucket数目已达上限~. ";
					else $message = $errcode."Bucket已创建，并为您保存好了~您现在就可以体验到OSS的极速模式了~~. ";
				}
				//var_dump($s);
			}
			update_option('enj0y_wordpress_oss', $_POST['options']);
			if ($_POST['options']['bucket']&&!$_POST['newbucket']) {
				$obj = new ALIOSS($_POST['options']['key'],$_POST['options']['secret']);
				$obj->set_debug_mode(FALSE);
				$options = get_option('enj0y_wordpress_oss');
                if (!in_array($_POST['options']['bucket'], $obj->listBuckets())) {

                } else {
                    if(!$message)$message = "设置已全部保存.您现在可以去体验下OSS带给您的快感了~. ";
                }
            } else {
				$obj = new ALIOSS($_POST['options']['key'],$_POST['options']['secret']);
				$obj->set_debug_mode(FALSE);
				$buckets = $obj->listBuckets();
				if ((!($buckets))&&$obj->signerr) {
					$message = "好像验证失败，请检查您提供的OSS的API ID和Secret Key是否正确.";
				}else{
					if(!$message)$message = "OSS验证信息已保存.";
					if((!$buckets)&&($message == "OSS验证信息已保存."))$message.="但你账户下没有可用的bucket，请到阿里云OSS体验站创建一个访问权限为“公共读”的bucket。";
				}
            }
        }

        $options = get_option('enj0y_wordpress_oss');
        if ($options['key'] && $options['secret']) {

			$obj = new ALIOSS($options['key'],$options['secret']);
			$obj->set_debug_mode(FALSE);
            if ((!($buckets = $obj->listBuckets()))&&$obj->signerr) {
                $message = "好像验证失败，请检查您提供的OSS的API ID和Secret Key是否正确.";
            }

			$buckets_readables = $obj->listreads($buckets);
            
        } elseif (!$options['key']) {
            $error = "请输入OSS的API ID.";
        } elseif (!$options['secret']) {
            $error = "请输入OSS的Secret Key.";
        }
		if($buckets=="UserDisable"){
					$message="你的OSS账户已被禁用，请登录阿里云官网OSS平台查看账户是否已欠费.<a href='http://i.aliyun.com/?from=oss4wp' target='_blank'>阿里云用户中心</a>";
					$buckets=false;
		}        
        
        include(dirname(__FILE__).'/admin-options.html');
    }
    

	/*
	Delete corresponding file from Aliyun oss
	*/
	function wp_delete_file($file) {

		$options = get_option('enj0y_wordpress_oss');
        
		if (!$options['wp-uploads'] || !$options['bucket'] || !$options['secret'] || !$options['bucket'] || !$options['ossdel']) {
		//配置不完整
            return $file;
        }
		
		$f_array = split ('/wp-content/uploads/', $file);
		$fileobject=$f_array[1];
		///print_r($file); ==> mnt/var/vcap.local/dea/apps/alpal-0-26addefdae73d4c2c7523f2a98ff4b60/app/wp-content/uploads/2012/08/19330043-150x150.jpg
		//print_r($f_array);==>
		/*
		Array
			(
				[0] => /mnt/var/vcap.local/dea/apps/alpal-0-26addefdae73d4c2c7523f2a98ff4b60/app
				[1] => 2012/08/a4e5d3e2-16ed-4ad3-bd6d-00cfad9c0f47-150x150.jpg
			)
		*/
		//if(!$del_files)global $del_files; 
		

		$obj = new ALIOSS($options['key'],$options['secret']);
		$obj->set_debug_mode(FALSE);
			/*
			$options = array(
				'quiet' => false,
			);
			*/
		$action=$obj->delete_object($options['bucket'],$fileobject);
		//$isok=$action->isOk()?'success':'failed'; //成功失败
		return $file;
	}

	function wp_get_attachment_metadata($data=false, $postID=false) {
		if (is_numeric($postID)) $this->meta = get_post_meta($postID, 'aliyunoss_info', true);
		return $data;
	}
    /*
    Handle uploads through default WordPress upload handler
    */
    function wp_update_attachment_metadata($data, $postID) {
		@set_time_limit(0);
        if (!$this->options) $this->options = get_option('enj0y_wordpress_oss');
        
        if (!$this->options['wp-uploads'] || !$this->options['bucket'] || !$this->options['secret']) {
            return $data;
        }
	        
		add_filter('option_siteurl', array(&$this, 'upload_path'));
        $uploadDir = wp_upload_dir();
		remove_filter('option_siteurl', array(&$this, 'upload_path'));
        $parts = parse_url($uploadDir['url']);
        
        $prefix = substr($parts['path'], 1) .'/';
        $type = get_post_mime_type($postID);

		$af=get_attached_file($postID, true);

		//echo "\r\ndata:";
		//print_r($data);
		//echo "\r\naf:";
		//print_r($af);

       	$data['file'] = mb_convert_encoding($af,'UTF-8','AUTO');
		//echo "\r\ndata[file]:";
		//print_r($data['file']);

		if (file_exists($data['file'])) {
			$file = array(
                'name' => substr($data['file'], strrpos($data['file'], '/') + 1), // basename($data['file']), //BUG发生于basename()
			    'type' => $type,
			    'tmp_name' => $data['file'],
			    'error' => 0,
			    'size' => filesize($data['file']),
			);
			//define('OSS_ACCESS_ID', $this->options['key']);
			//define('OSS_ACCESS_KEY', $this->options['secret']);
			$obj = new ALIOSS($this->options['key'],$this->options['secret']);
			$obj->set_debug_mode(FALSE);

			$p=date('Y/m/');
			if ($obj->putObjectStream($this->options['bucket'], $p.$file['name'], $file ,$this->options['expires'], 'public-read', array(), false, $this->options['smush-it'], $this->options['ignore-gif'])) {
			    
			    if ($data['thumb']) {
			        $thumbpath = str_replace( basename( $data['file'] ), $data['thumb'], $data['file'] );
			        $filethumb = array(
                        'name' => $data['thumb'],
                        'type' => $type,
                        'tmp_name' => $thumbpath,
                        'size' => filesize($thumbpath),
			        );
			        
			        $obj->putObjectStream($this->options['bucket'], $p.$filethumb['name'], $filethumb ,$this->options['expires'], 'public-read', array(), false, $this->options['smush-it'], $this->options['ignore-gif']);
			    } elseif (count($data['sizes'])) foreach ($data['sizes'] as $altName => $altSize) {
					$altPath = str_replace( basename( $data['file'] ), $altSize['file'], $data['file'] );
					$altMeta = array(
						'name' => $altSize['file'],
						'type' => $type,
						'tmp_name' => $altPath,
						'size' => filesize($altPath),
						);
			        $obj->putObjectStream($this->options['bucket'], $p.$altMeta['name'], $altMeta ,$this->options['expires'], 'public-read', array(), false, $this->options['smush-it'], $this->options['ignore-gif']);
				}
			    
			    
			    delete_post_meta($postID, 'aliyunoss_info');
                add_post_meta($postID, 'aliyunoss_info', array(
                    'bucket' => $this->options['bucket'],
                    'key' => $prefix.$file['name']
                    ));
			} else {
			    
			}
		}

		/*
			Fixed the wpstored bug.
		*/
		if(!$this->options['wpstore']){
			@unlink($data['file']);
			$storedir=dirname($data['file'])."/";
			$size_array=array('thumbnail','medium','large','post-thumbnail','large-thumbnail','medium-thumbnail','small-thumbnail');
			foreach ( $size_array as $xsize ){
				if(file_exists($storedir.$data['sizes'][$xsize]['file'])){@unlink($storedir.$data['sizes'][$xsize]['file']);}
			}
		}
        return $data;
    }
    function wp_handle_upload($info) {
        return $info;
    }

	// figure out the correct path to upload to, for wordpress mu installs
	function upload_path($path='') {
		global $current_blog;
		if (!$current_blog) return $path;
        if ($current_blog->path == '/' && ($current_blog->blog_id != 1)) {
			$dir = substr($current_blog->domain, 0, strpos($current_blog->domain, '.'));
		} else {
		    // prepend a directory onto the path for vhosted blogs
		    if (constant("VHOST") != 'yes') {
		        $dir = '';
		    } else {
		        $dir = $current_blog->path;
		    }
		}
		//echo trim($path.'/'.$dir, '/');
		if ($path == '') {
		    $path = $current_blog->path;
		}
		return trim($path.'/'.$dir, '/');
	}
	function media_buttons($context) {
		global $post_ID, $temp_ID;
		$dir = dirname(__FILE__);
		$pluginRootURL = get_option('siteurl').substr($dir, strpos($dir, '/wp-content'));
		$image_btn = '../wp-content/plugins/oss4wp/wordpress-oss/database.png';
		$image_title = '上传到阿里云OSS';
		
		$uploading_iframe_ID = (int) (0 == $post_ID ? $temp_ID : $post_ID);

		$media_upload_iframe_src = "media-upload.php?post_id=$uploading_iframe_ID";
		$out = ' <a href="'.$media_upload_iframe_src.'&tab=Enj0y-wordpress-oss&TB_iframe=true&height=500&width=640" class="thickbox" title="'.$image_title.'"><img src="'.$image_btn.'" alt="'.$image_title.'" /></a>';
		return $context.$out;
	}
	function media_upload_content() {
        $this->upload_files_Enj0y_aliyunoss(); // process any uploaded files or new folders
            
		if (!$this->options) $this->options = get_option('enj0y_wordpress_oss');
		//if (!is_object($this->oss)) {
	        require_once(dirname(__FILE__).'/sdk.class.php');

			
			define('OSS_ACCESS_ID', $this->options['key']);
			//ACCESS_KEY
			define('OSS_ACCESS_KEY', $this->options['secret']);
			$obj = new ALIOSS($this->options['key'],$this->options['secret']);
			$obj->set_debug_mode(FALSE);
        //}
        
		wp_iframe(array(&$this, 'tab'));
	}
    /*
    Display tabs
    */
    function addPhotosTab() {
        add_filter('wp_upload_tabs', array(&$this, 'wp_upload_tabs'));
        add_action('upload_files_Enj0y_aliyunoss', array(&$this, 'upload_files_Enj0y_aliyunoss'));
        add_action('upload_files_upload', array(&$this, 'upload_files_upload'));
    }
    function wp_upload_tabs ($array) {
    /*
        0 => tab display name, 
        1 => required cap, 
        2 => function that produces tab content, 
        3 => total number objects OR array(total, objects per page), 
        4 => add_query_args
	*/
        if (!$this->options) $this->options = get_option('enj0y_wordpress_oss');
        require_once(dirname(__FILE__).'/sdk.class.php');
        

        if ($this->options['key'] && $this->options['secret'] && $this->options['bucket']) {
            $paged = array();
	        $args = array('prefix' => ''); // this doesn't do anything in WP 2.1.2
            $tab = array(
                'Enj0y_aliyunoss' => array('Aliyun oss', 'upload_files', array(&$this, 'tab'), $paged, $args),
                //'Enj0y_aliyunoss_upload' => array('Upload oss', 'upload_files', array(&$this, 'upload'), $paged, $args),
                );

            return array_merge($array, $tab);
        } else {
            return $array;
        }
    }

    function upload_files_upload() {
        // javascript here to inject javascript and allow the upload from to post to Aliyun oss instead
    }
	
    function upload_files_Enj0y_aliyunoss() {
		global $current_blog;
		$restrictPrefix = ''; // restrict to a selected prefix in current bucket
		if ($current_blog)  { // if wordpress mu
        	$restrictPrefix = ltrim($this->upload_path().'/files/', '/');
		}
	
		if (is_array($_FILES['newfile'])) {
			$file = $_FILES['newfile']; //$_FILES["file"]["tmp_name"]
	        if (!$this->options) $this->options = get_option('enj0y_wordpress_oss');
	        
			$obj = new ALIOSS($this->options['key'],$this->options['secret']);

			$bucket = $this->options['bucket'];
			$object = $file['name'];
			
			$content  = file_get_contents($_FILES["file"]["tmp_name"]);
	
			$upload_file_options = array(
				'content' => $content,
				'length' => strlen($content),
				ALIOSS::OSS_HEADERS => array(
					'Cache-control' => 'max-age=864000',
				),
			);
			
			$response = $obj->upload_file_by_content($bucket,$object,$upload_file_options);
		}
		if ($_POST['newfolder']){
			if (!$this->options) $this->options = get_option('enj0y_wordpress_oss');
	        require_once(dirname(__FILE__).'/lib.oss.php');

			$obj = new ALIOSS($this->options['key'],$this->options['secret']);

			$obj->create_object_dir($this->options['bucket'], $restrictPrefix.$_POST['prefix'].$_POST['newfolder']);
		}
    }
    
    function getErrorMessage($parsedxml, $responseCode){
    	$message = 'Error '.$responseCode.': ' . $parsedxml->Message;
    	if(isset($parsedxml->StringToSignBytes)) $message .= "<br>Hex-endcoded string to sign: " . $parsedxml->StringToSignBytes;
    	return $message;
    }


}
?>