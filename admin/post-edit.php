<?php
date_default_timezone_set('Asia/Shanghai');
ini_set("display_errors", "On"); error_reporting(E_ALL);
require_once '../files/conf.php';
if (isset($_COOKIE['mc_token'])) {
  $token = $_COOKIE['mc_token'];
  if ($token != md5($mc_config['user_name'].'_'.$mc_config['user_pass'])) {
    Header("Location:index.php");
  }
} else {
  Header("Location:index.php");
}
$page_file = basename($_SERVER['PHP_SELF']);
function shorturl($input) {
  $base32 = array (
    'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
    'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
    'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
    'y', 'z', '0', '1', '2', '3', '4', '5'
  );
  $hex = md5('prefix'.$input.'surfix'.time());
  $hexLen = strlen($hex);

  $subHexLen = $hexLen / 8;
  $output = array();

  for ($i = 0; $i < $subHexLen; $i++) {
    $subHex = substr ($hex, $i * 8, 8);
    // PHP7开始，含十六进制字符串不再被认为是数字
    // 如果非要检测字符串是否含十六进制数字，官方建议的代码是
    // http://www.bjphper.com/?post/12bip0
    // $int = 0x3FFFFFFF & (1 * ('0x'.$subHex));
    $int = 0x3FFFFFFF & (1 * (filter_var("0x" . $subHex, FILTER_VALIDATE_INT, FILTER_FLAG_ALLOW_HEX)));
    $out = '';
    for ($j = 0; $j < 6; $j++) {
      $val = 0x0000001F & $int;
      $out .= $base32[$val];
      $int = $int >> 5;
    }
    $output[] = $out;
  }
  return $output;
}
function post_sort($a, $b) {
  $a_date = $a['date'];
  $b_date = $b['date'];
  if ($a_date != $b_date)
    return $a_date > $b_date ? -1 : 1;
  return $a['time'] > $b['time'] ? -1 : 1;
}

$post_id          = '';
$post_state       = '';
$post_title       = '';
$post_content     = '';
$post_tags        = array();
$post_date        = '';
$post_time        = '';
$post_can_comment = '';
$post_format      = '';
$error_msg        = '';
$succeed          = false;
//if form is post, add or edit submit
if (isset($_POST['_IS_POST_BACK_'])) {
  $post_id          = $_POST['id'];
  $post_state       = $_POST['state'];
  $post_title       = trim($_POST['title']);
  $post_content     = get_magic_quotes_gpc() ? stripslashes(trim($_POST['content'])) : trim($_POST['content']);
  $tmp_tags = $_POST['tags'] ?$_POST['tags']: "default";
  $post_tags        = explode(',', trim($tmp_tags));
  $post_date        = date("Y-m-d");
  $post_time        = date("H:i:s");
  $post_can_comment  = $_POST['can_comment'];
  $post_format  = $_POST['format'];
  if ($_POST['date'] != '')
    $post_date = $_POST['date'];
  if ($_POST['time'] != '')
    $post_time = $_POST['time'];
  $post_tags_count = count($post_tags);
  for ($i = 0; $i < $post_tags_count; $i ++) {
    $trim = trim($post_tags[$i]);
    if ($trim == '') {
      unset($post_tags[$i]);
    } else {
      $post_tags[$i] = $trim;
    }
  }
  reset($post_tags);
  if ($post_title == '') {
    $error_msg = '文章标题不能为空';
  }
  else {
    if ($post_id == '') {
      $file_names = shorturl($post_title);
      foreach ($file_names as $file_name) {
        $file_path = '../files/posts/data/'.$file_name.'.dat';
        if (!is_file($file_path)) {
          $post_id = $file_name;
          break;
        }
      }
    }
    else {
      $file_path = '../files/posts/data/'.$post_id.'.dat';
      $data = unserialize(file_get_contents($file_path));
      $post_old_state = $data['state'];
      // 状态变化，publish to delete, or delete to publish
      if ($post_old_state != $post_state) {
        $index_file = '../files/posts/index/'.$post_old_state.'.php';
        require $index_file;
        unset($mc_posts[$post_id]);
        file_put_contents($index_file,
          "<?php\n\$mc_posts=".var_export($mc_posts, true)."\n?>"
        );
      }
    }
    $data = array(
      'id'          => $post_id,
      'state'       => $post_state,
      'title'       => $post_title,
      'tags'        => $post_tags,
      'date'        => $post_date,
      'time'        => $post_time,
      'format'      => $post_format,
      'can_comment' => $post_can_comment,
    );
    $index_file = '../files/posts/index/'.$post_state.'.php';
    require $index_file;
    $mc_posts[$post_id] = $data;
    uasort($mc_posts, "post_sort");
    file_put_contents($index_file,
      "<?php\n\$mc_posts=".var_export($mc_posts, true)."\n?>"
    );
    $data['content'] = $post_content;
    file_put_contents($file_path, serialize($data));
    $succeed = true;
  }
} else if (isset($_GET['id'])) {
  $file_path = '../files/posts/data/'.$_GET['id'].'.dat';
  $data = unserialize(file_get_contents($file_path));
  $post_id      = $data['id'];
  $post_state   = $data['state'];
  $post_title   = $data['title'];
  $post_content = $data['content'];
  $post_tags    = $data['tags'];
  $post_date    = $data['date'];
  $post_time    = $data['time'];
  $post_format  = isset($data['format']) ? $data['format'] : '1';
  $post_can_comment = isset($data['can_comment']) ? $data['can_comment'] : '1';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <title>后台管理</title>
  <link style="text/css" rel="stylesheet" href="style.css" />
  <link rel="stylesheet" type="text/css" href="/github/editor-md/css/editormd.css">
  <link style="text/css" rel="stylesheet" href="../files/theme/v/markdown.css" />
  <style type="text/css">
    html, body{
      height: 100%;
    }
  </style>
</head>
<body id="post-edit">

<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" style="height: 100%">
  <input type="hidden" name="_IS_POST_BACK_" value=""/>
  <input type="hidden" name="format" value="2"/>
  <input type="hidden" name="date" value="<?php echo $post_date; ?>"/>
  <input type="hidden" name="time" value="<?php echo $post_time; ?>"/>
  <input type="hidden" name="can_comment" value="1"/>
  <input type="hidden" name="state" value="publish"/>
  <input type="hidden" name="id" value="<?php echo $post_id; ?>"/>

  <?php if ($succeed) { ?>
  <?php if ($post_state == 'publish') { ?>
  <div class="updated message">文章已发布 <?php echo date("Y-m-d H:i"); ?> <a href="../?post/<?php echo $post_id; ?>" target="_blank">查看文章</a></div>
  <?php } else { ?>
  <div class="updated message">文章已保存到“草稿箱”。 <a href="post.php?state=draft">打开草稿箱</a></div>
  <?php } ?>
  <?php } ?>
  <?php if ($error_msg) {?>
    <div class="updated error"><?php echo $error_msg; ?></div>
  <?php } ?>
  <div class="admin_page_name">
    <input name="title" type="text" placeholder="输入标题" class="edit_textbox mock_input title" value="<?php
    if ($post_title !== "") {
      echo htmlspecialchars($post_title);
    }
    ?>"/>
    <div style="position: absolute; right:10px;top:10px;">
      <input name="tags" type="text" style="display: inline-block;" class="mock_input" placeholder="输入标签" value="<?php
      if (count($post_tags)) {
        echo htmlspecialchars(implode(',', $post_tags));
      }
      ?>"/>
      <input type="button" name="save" value="草稿" class="btn btn-default" onclick="save_draft()" />
      <input type="submit" name="save" value="保存" class="btn btn-default" />
      <a href="post.php" class="btn btn-default">返回</a>
      <a href="../?archive/" target="_blank" class="btn btn-default">档案</a>
    </div>
  </div>

  <div style="margin-bottom:0;" id="editor_container">
    <textarea id="elm1" name="content" style="width: 860px; height: 450px; display: none; "><?php echo htmlspecialchars($post_content); ?></textarea>
  </div>

</form>
<script type="text/javascript" src="/github/editor-md/examples/js/jquery.min.js"></script>
<script type="text/javascript" src="/github/editor-md/editormd.min.js"></script>
<script type="text/javascript">

  function save_draft() {
    $('input[name=state]').val('draft');
    $('form').submit();
  }

  $(pageInit);
  let mdEditor;
  function pageInit(){
    mdEditor = editormd("editor_container", {
        width: '99%',
        height  : 'calc(100% - 50px',
        emoji : false,
        fontSize: '16px',
        lineNumbers: false,
        htmlDecode : true,
        toolbarIcons : function() {
            return [
              "bold", "del", "italic", "quote", "ucwords", "uppercase", "lowercase", "|",
              "list-ul", "list-ol", "hr", "|",
              "link", "reference-link", "image", "code", "preformatted-text", "code-block", "table", "datetime", "pagebreak", "|",
              "watch", "preview", "clear", "|",
              "help"
          ]
        },
        syncScrolling : "single",
        path : '/github/editor-md/lib/',
        imageUpload    : true,
        imageFormats   : ["jpg", "jpeg", "gif", "png", "bmp", "webp"],
        imageUploadURL : "./img-upload.php",
        onload : function() {}
    });
    document.getElementById('editor_container').onpaste = function(e){
      if ( e.clipboardData.items ) {
        ele = e.clipboardData.items
        for (var i = 0; i < ele.length; ++i) {
            if ( ele[i].kind == 'file' && ele[i].type.indexOf('image/') !== -1 ) {
                var blob = ele[i].getAsFile();
                if ( !window.FormData ) {
                    alert('not support window.FormData may not upload file');
                } else {
                    var formData = new FormData();
                    formData.append('editormd-image-file', blob);

                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', './img-upload.php', true);

                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            let res = JSON.parse(this.responseText);
                            mdEditor.insertValue(`![](${res.url})`);
                        } else {
                            console.log('upload failed');
                        }
                    }
                    xhr.send(formData);
                }
            }
        }
      }
    };

    document.addEventListener('keyup', e => {
      if (e.ctrlKey && e.keyCode == 83) {
        document.forms[0].submit();
      }
    })
  }
</script>
</body>
</html>