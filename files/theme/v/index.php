<?php if (!isset($mc_config)) exit; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset=utf-8>
<meta name=viewport content="width=device-width,initial-scale=1,user-scalable=no,viewport-fit=cover">
<meta name="description" content="<?php mc_the_title();?>" />
<title><?php if (mc_is_post() || mc_is_page()) { mc_the_title(); ?> | <?php mc_site_name(); } else { mc_site_name(); ?> | <?php mc_site_desc(); }?></title>
<meta name="keywords" content="<?php mc_the_onlytags(); ?>"/>
<link rel="stylesheet" type="text/css" href="third-party/editor-md/css/editormd.css">
<link href="<?php mc_theme_url('style.css'); ?>" type="text/css" rel="stylesheet"/>

</head>
<body class="skin9<?php if(isMobile()) echo ' mobile'; ?>">
<div id="main">
  <div id="header">
    <div id="sitename">
      <a href="<?php mc_site_link(); ?>" title="<?php mc_site_desc(); ?>"><?php mc_site_name(); ?></a>
    </div>
    <div id="navbar">
      <a href="<?php mc_site_link(); ?>/" class="home" title="首页">首页</a>
      <a href="<?php mc_site_link(); ?>/?archive/" class="archive" title="文章">文章</a>
      <a href="<?php mc_site_link(); ?>/?rss/" class="rss" title="RSS订阅" target="_blank">RSS订阅</a>
    </div>
  </div>
  <div class="clear"></div>
  <div id="content">
    <div id="content_box">
      <?php if (mc_is_post()) { ?>
      <div class="post">
        <h1 class="title"><?php mc_edit_link(); ?></h1>
        <div class="post_meta">
          <div class="post_date"><?php mc_the_date(); ?></div>
          <div class="post_tag"><?php mc_the_tags('','',''); ?></div>
        </div>
        <hr class="page-break editormd-page-break">
        <div class="content" style="min-height:300px;" id="post_content">
          <?php if (mc_is_md()) { ?>
            <textarea id="md_content" style="display:none;"><?php mc_the_content(); ?></textarea>
          <?php } else { ?>
            <?php mc_the_content(); ?>
          <?php }?>
        </div>

      </div>
        <?php if (mc_can_comment()) { ?>
        <div id="comm">
            <!-- 多说评论框 start -->
            <div class="ds-thread" data-thread-key="<?php echo $mc_post_id;?>" data-title="<?php mc_the_title(); ?>" data-url=""></div>
            <!-- 多说评论框 end -->
            <?php mc_comment_code(); ?></div>
        <?php } ?>
      <?php } else if (mc_is_page()) { ?>
      <div class="post">
        <h1 class="title"><?php mc_page_title(); ?></h1>
        <div class="content">
          <?php mc_the_content(); ?>
        </div>
      </div>
        <?php if (mc_can_comment()) { ?>
        <div id="comm"><?php mc_comment_code(); ?></div>
        <?php } ?>
      <?php } else if (mc_is_archive()) { ?>
      <div class="post">
        <h1 class="title">文章存档</h1>
        <div class="content">
            <table width="" border="0" align="center" cellpadding="0" cellspacing="0" style="margin:30px auto;">
                <tbody>
                    <tr>
                        <td width="160" style="vertical-align:top;">
                            <h1 class="date_list">月份</h1>
                            <ul class="list"><?php mc_date_list(); ?></ul>
                        </td>
                        <td width="500" style="vertical-align:top;">
                            <h1 class="tag_list">标签</h1>
                            <ul class="list"><?php mc_tag_list(); ?></ul>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
      </div>
      <?php } else { ?>
      <?php if (mc_is_tag()) { ?>
      <div id="page_info"><span><?php mc_tag_name(); ?></span></div>
      <?php } else if (mc_is_date()) { ?>
      <div id="page_info"><span><?php mc_date_name(); ?></span></div>
      <?php } ?>
      <h3>共 <?php echo $mc_post_count; ?> 篇</h3>
      <?php while (mc_next_post()) { ?>

      <p>
        <strong>
          <?php mc_the_link(); ?>
        </strong>

        [ <?php mc_the_date(); ?> ]
        [ <?php mc_the_tags(); ?> ]
      </p>

      <?php } ?>
      <div class="clear"></div>

      <?php } ?>
    </div>
  </div>
</div>
  <div id="page_bar">
        <?php if (mc_has_new()) { ?><?php mc_goto_new('&lt;-'); ?><?php } ?>
        <?php if (mc_has_old()) { ?><?php mc_goto_old('-&gt;'); ?><?php } ?>
  </div>

<div class="footer">李玉俭----2012-11-02修改 <?php mc_site_desc();?></div>
<script type="text/javascript" src="admin/xheditor/jquery.js"></script>
<script type="text/javascript" src="third-party/editor-md/lib/marked.min.js"></script>
<script type="text/javascript" src="third-party/editor-md/lib/prettify.min.js"></script>
<script type="text/javascript" src="third-party/editor-md/lib/underscore.min.js"></script>
<script type="text/javascript" src="third-party/editor-md/lib/flowchart.min.js"></script>
<script type="text/javascript" src="third-party/editor-md/lib/jquery.flowchart.min.js"></script>
<script type="text/javascript" src="third-party/editor-md/lib/raphael.min.js"></script>
<script type="text/javascript" src="third-party/editor-md/lib/sequence-diagram.min.js"></script>
<script type="text/javascript" src="third-party/editor-md/editormd.min.js"></script>
<script>
  window.onload = function(){
    <?php if (mc_is_md()) { ?>
    testEditormdView = editormd.markdownToHTML("post_content", {
        markdown        :  $("#md_content").text(), // "\r\n" +
        htmlDecode      : true,       // 开启 HTML 标签解析，为了安全性，默认不开启
        // htmlDecode      : "style,script,iframe",  // you can filter tags decode
        //toc             : false,
        tocm            : true,    // Using [TOCM]
        //tocContainer    : "#custom-toc-container", // 自定义 ToC 容器层
        //gfm             : false,
        //tocDropdown     : true,
        // markdownSourceCode : true, // 是否保留 Markdown 源码，即是否删除保存源码的 Textarea 标签
        emoji           : true,
        taskList        : true,
        tex             : true,  // 默认不解析
        flowChart       : true,  // 默认不解析
        sequenceDiagram : true,  // 默认不解析
    });
    <?php } ?>

  }
  function switcherSkin (ele) {
    // console.log(ele);
    var a_arr = document.querySelectorAll('#skinbar a');
    var cn = ele.className;
    for (var i = a_arr.length - 1; i >= 0; i--) {
      var a = a_arr[i], c = a.className;
      if (a_arr[i] === ele) {
        console.log(ele);
        a.className += ' cur';
      } else {
        a.className = c.split(' ')[0];
      }
    };
    document.getElementsByTagName('body')[0].className = cn;
    if (window.localStorage) {
      localStorage.setItem('body-skin', cn);
    };
  }
  function q(e){
    return document.querySelector(e);
  }
  document.onkeydown = function(event){
    var href = '';
    if (event.keyCode == 39) {
      href = q('.nextpage').href;
      window.location.href = href;
    }
    if (event.keyCode == 37) {
      href = q('.prevpage').href;
      window.location.href = href;
    }
  }
</script>
</body>
</html>
