<?php get_header(); ?>
<script type="text/javascript">
  $(document).ready(function(){
    $("#approve").iphoneStyle({
      onChange: function(e, checked){
        if(checked){
          $(e).attr("checked", "checked");
        }else{
          $(e).removeAttr("checked");
        }
      }
    });
    $("input[type=checkbox].ibtn").wrap("<div class='custom-check'>");
    $("input[type=checkbox].ibtn").each(function(){
      if($(this).is(":checked")){
        $(this).parent(".custom-check").addClass("active");
      }
    });
    $(".custom-check").on("click", function(){
      if($(this).hasClass("active")){
        $(this).removeClass("active");
        $(this).children("input[type=checkbox].ibtn").removeAttr("checked");
      }else{
        $(this).addClass("active");
        $(this).children("input[type=checkbox].ibtn").attr("checked", "checked");
      }
    });
  });
</script>
<div class="body">
  <div class="content">
    <h2 class="rama-title">User role</h2>
    <?php echo $this->session->flashdata('message'); ?>
    <?php echo form_open($action);
    ?>
    <table width="100%">
      <tr>
        <td width="20%">Role name</td>
        <td>
          <div class="span3"><?php echo form_input($role_name); ?></div>
        </td>
      </tr>
      <tr>
        <td>Roled modules</td>
        <td>
          <ul class="dashed-li">
          <li>
            <div class="span3"><strong>Module</strong></div>
            <div class="span1"><strong>Add</strong></div>
            <div class="span1"><strong>Edit</strong></div>
            <div class="span1"><strong>Delete</strong></div>
            <div class="span1"><strong>Approve</strong></div>
            <div class="span1"><strong><a href="#" class="bootstrap-tooltip" data-placement="top" data-title="this is used for listing the entry, if unchecked then it will display nothing">Select</a></strong></div>
            <br class="cl" />
          </li>
          <?php
            // buka directory controllers
            $path = APPPATH."controllers";
            $dir = dir($path);
            while(($controllers = $dir->read()) !== false){
              if(preg_match("/~/", $controllers) == false && !is_dir($controllers)){ // skip directory (.|..) and backup file
                $controller = explode(".", $controllers);
                $controller = $controller[0];
                $controller = ucwords(str_replace("_", " ", $controller));
                $controller = str_replace(" ", "_", $controller);
                $is_add = "";
                if(isset($role_detail["add_{$controller}"]) && $role_detail["add_{$controller}"]==1){
                  $is_add = "checked";
                }
                $is_edit = "";
                if(isset($role_detail["edit_{$controller}"]) && $role_detail["edit_{$controller}"]==1){
                  $is_edit = "checked";
                }
                $is_delete = "";
                if(isset($role_detail["delete_{$controller}"]) && $role_detail["delete_{$controller}"]==1){
                  $is_delete = "checked";
                }
                $is_approve = "";
                if(isset($role_detail["approve_{$controller}"]) && $role_detail["approve_{$controller}"]==1){
                  $is_approve = "checked";
                }
                $is_select = "";
                if(isset($role_detail["select_{$controller}"]) && $role_detail["select_{$controller}"]==1){
                  $is_select = "checked";
                }
                ?>
                <li>
                  <div class="span3">
                    <?php echo $controller; ?>
                    <input type="hidden" name="modules[]" value="<?php echo $controller; ?>">
                  </div>
                  <div class="span1"><input type="checkbox" name="add_<?php echo $controller; ?>" class="ibtn" <?php echo $is_add; ?>/></div>
                  <div class="span1"><input type="checkbox" name="edit_<?php echo $controller; ?>" class="ibtn" <?php echo $is_edit; ?>/></div>
                  <div class="span1"><input type="checkbox" name="delete_<?php echo $controller; ?>" class="ibtn" <?php echo $is_delete; ?>/></div>
                  <div class="span1"><input type="checkbox" name="approve_<?php echo $controller; ?>" class="ibtn" <?php echo $is_approve; ?>/></div>
                  <div class="span1"><input type="checkbox" name="select_<?php echo $controller; ?>" class="ibtn" <?php echo $is_select; ?>/></div>
                  <br class="cl" />
                </li>
                <?php
              }
            }
            $dir->close();
            ?></ul>
        </td>
      </tr>
    </table>
    <?php echo form_submit($btn_save); ?>
    <?php echo form_close(); ?>
  </div>
</div>
<?php get_footer(); ?>
