<?php get_header(); ?>
<?php

function HeaderLink($value, $key, $col, $dir) {
    $out = "<a href=\"" . site_url('taxes_employees') . "?c=";
    //set column query string value
    switch ($key) {
        case "sp_status":
            $out .= "1";
            break;
        case "sp_ptkp":
            $out .= "2";
            break;
        case "sp_id":
            $out .= "3";
            break;
        default:
            $out .= "0";
    }

    $out .= "&d=";

    //reverse sort if the current column is clicked
    if ($key == $col) {
        switch ($dir) {
            case "ASC":
                $out .= "1";
                break;
            default:
                $out .= "0";
        }
    } else {
        //pass on current sort direction
        switch ($dir) {
            case "ASC":
                $out .= "0";
                break;
            default:
                $out .= "1";
        }
    }

    //complete link
    $out .= "\">$value</a>";

    return $out;
}
?>

<div class="body">
    <div class="content">
        <?php echo $this->session->flashdata('message'); ?>
        <div class="page-header">
            <div class="icon">
                <span class="ico-credit"></span>
            </div>
            <h1>Tax Status
                <small>Manage tax status</small>
            </h1>
        </div>
        <br class="cl" />
        <div class="head blue">
            <?php echo header_btn_group("#", "taxes_employees/add"); ?>
        </div>
        <div id="search_bar" class="widget-header">
            <?php search_form(array("" => "By", "sp_status" => "Status", "sp_ptkp" => "PTKP")); ?>
        </div>
        <table class="table fpTable table-hover">
            <thead>
                <tr>
                    <th width="10%"><?php echo HeaderLink("SP ID", "sp_id", $col, $dir); ?></th>
                    <th width="10%"><?php echo HeaderLink("SP Status", "sp_status", $col, $dir); ?></th>
                    <th width="30%"><?php echo HeaderLink("Nilai Penghasilan Tidak Kena Pajak", "sp_ptkp", $col, $dir); ?></th>
                    <th width="45%">Keterangan</th>
                    <th width="5%" class="action_cell">Action</th>
                </tr>
            </thead>
            <?php
            foreach ($tax_list as $row) {
            ?>
                <tr>
                    <td><?php echo $row->sp_id; ?></td>
                    <td><?php echo $row->sp_status; ?></td>
                    <td class="ta_right"><?php echo "Rp. " . rupiah($row->sp_ptkp); ?></td>
                    <td><?php echo $row->sp_note; ?></td>
                    <td class="action_cell">
                    <?php btn_action('taxes_employees/edit/' . $row->sp_id, "Edit taxes", "taxes_employees/delete/" . $row->sp_id); ?>
                </td>
            </tr>
            <?php } ?>
            </table>
            <div class="clearfix"></div>
            <br>
            <div class="pagination pagination-right">
                <ul>
                <?php echo $pagination; ?>
            </ul>
        </div>
    </div>
</div>

<?php get_footer(); ?>
