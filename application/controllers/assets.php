<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Assets extends CI_Controller {

    private $limit = 10;

    function __construct() {
        parent::__construct();
        $this->load->model('Asset');
        $this->load->model('Asset_Detail');
        $this->sess_username = $this->session->userdata('username');
        $this->sess_role_id = $this->session->userdata('sess_role_id');
        $this->sess_staff_id = $this->session->userdata('sess_staff_id');
        $this->session->userdata('logged_in') == true ? '' : redirect('users/sign_in');
    }

    public function index($offset = 0) {
        $this->filter_access('Assets', 'roled_select', base_url());

        $asset_list = new Asset();
        $data['staff'] = new Staff();

        switch ($this->input->get('c')) {
            case "1":
                $data['col'] = "asset_name";
                break;
            case "2":
                $data['col'] = "asset_status";
                break;
            case "3":
                $data['col'] = "staff_id";
                break;
            case "4":
                $data['col'] = "date";
                break;
            case "5":
                $data['col'] = "asset_id";
                break;
            default:
                $data['col'] = "asset_id";
        }

        if ($this->input->get('d') == "1") {
            $data['dir'] = "DESC";
        } else {
            $data['dir'] = "ASC";
        }

        $total_rows = $asset_list->count();
        $data['title'] = "Assets";
        $data['btn_add'] = anchor('assets/add', 'Add New', array('class' => 'btn btn-primary'));
        $data['btn_home'] = anchor(base_url(), 'Home', array('class' => 'btn'));

        $uri_segment = 3;
        $offset = $this->uri->segment($uri_segment);

        $asset_list->order_by($data['col'], $data['dir']);
        $data['asset_list'] = $asset_list->get($this->limit, $offset)->all;

        $config['base_url'] = site_url("assets/index");
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $this->limit;
        $config['uri_segment'] = $uri_segment;
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('assets/index', $data);
    }

    function add() {
        $this->filter_access('Assets', 'roled_add', 'assets/index');

        $data['title'] = 'Add New Asset';
        $data['form_action'] = site_url('assets/save');
        $data['link_back'] = anchor('assets/', 'Back', array('class' => 'btn'));

        $data['id'] = '';
        $data['asset_name'] = array('name' => 'asset_name');
        $options_status = array(
            '1' => 'Enable',
            '0' => 'Disable'
        );
        $status_selected = '1';
        $data['asset_status'] = form_dropdown('asset_status', $options_status, $status_selected);

        // Staffs
        $staff = new Staff();
        $list_staff = $staff->list_drop();
        $staff_selected = '';
        $data['staff_id'] = form_dropdown('staff_id', $list_staff, $staff_selected);

        $data['date'] = array('name' => 'date', 'id' => 'date');
        $data['btn_save'] = array('name' => 'btn_save', 'value' => 'Save', 'class' => 'btn btn-primary');

        $this->load->view('assets/frm_assets', $data);
    }

    function edit($id) {
        $this->filter_access('Assets', 'roled_edit', 'assets/index');
        $asset = new Asset();
        $rs = $asset->where('asset_id', $id)->get();
        $data['id'] = $rs->asset_id;
        $data['asset_name'] = array('name' => 'asset_name', 'value' => $rs->asset_name);
        $options_status = array(
            '1' => 'Enable',
            '0' => 'Disable'
        );
        $status_selected = $rs->asset_status;
        $data['asset_status'] = form_dropdown('asset_status', $options_status, $status_selected);

        // Staffs
        $staff = new Staff();
        $list_staff = $staff->list_drop();
        $staff_selected = $rs->staff_id;
        $data['staff_id'] = form_dropdown('staff_id', $list_staff, $staff_selected);


        $data['date'] = array('name' => 'date', 'id' => 'date', 'value' => $rs->date);
        $data['btn_save'] = array('name' => 'btn_save', 'value' => 'Update', 'class' => 'btn btn-primary');

        $data['title'] = 'Update';
        $data['form_action'] = site_url('assets/update');
        $data['link_back'] = anchor('assets/', 'Back', array('class' => 'btn'));

        $this->load->view('assets/frm_assets', $data);
    }

    function save() {
        $this->filter_access('Assets', 'roled_add', 'assets/index');
        $asset = new Asset();
        $asset->asset_name = $this->input->post('asset_name');
        $asset->asset_status = $this->input->post('asset_status');
        $asset->staff_id = $this->input->post('staff_id');
        $asset->date = $this->input->post('date');
        if ($asset->save()) {
            $this->session->set_flashdata('message', 'Asset successfully created!');
            redirect('assets/');
        } else {
            // Failed
            $asset->error_message('custom', 'Field required');
            $msg = $asset->error->custom;
            $this->session->set_flashdata('message', $msg);
            redirect('assets/add');
        }
    }

    function update() {
        $this->filter_access('Assets', 'roled_edit', 'assets/index');
        $asset = new Asset();
        $asset->where('asset_id', $this->input->post('id'))
                ->update(array(
                    'asset_name' => $this->input->post('asset_name'),
                    'asset_status' => $this->input->post('asset_status'),
                    'staff_id' => $this->input->post('staff_id'),
                    'date' => $this->input->post('date')
                        )
        );

        $this->session->set_flashdata('message', 'Asset Update successfuly.');
        redirect('assets/');
    }

    function delete($id) {
        $this->filter_access('Assets', 'roled_delete', 'assets/index');
        $asset = new Asset();
        $asset->_delete($id);

        $this->session->set_flashdata('message', 'Asset successfully deleted!');
        redirect('assets/');
    }

    function filter_access($module, $field, $page) {
        $user = new User();
        $status_access = $user->get_access($this->sess_role_id, $module, $field);

        if ($status_access == false) {
            $msg = '<div class="alert alert-error">You do not have access to this page, please contact administrator</div>';
            $this->session->set_flashdata('message', $msg);
            redirect($page);
        }
    }

}