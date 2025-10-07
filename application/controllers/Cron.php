<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends MY_Controller {
    
    function backup($tipe = 'all') {
		ini_set('memory_limit', '-1');

        if(in_array($tipe, ['all','db'])) {
            $backupdir = FCPATH . 'assets/backup/backup_'.date('Y_m_d_h_i');
            if(!is_dir($backupdir)) mkdir($backupdir, 0777, true);
            
            $table = db_list_table();
            $this->load->dbutil();
            $this->load->helper('file');
            foreach($table as $t) {
                $prefs = array(
                    'tables'      => array($t),
                    'format'      => 'sql',
                    'filename'    => $t.'.sql'
                );
                $backup		= $this->dbutil->backup($prefs);
                $db_name 	= $t.'.sql';
                $save 		= $backupdir.'/'.$db_name;
                write_file($save, $backup);
            }
        }
        if(in_array($tipe, ['all','file'])) {
            $conf       = [
                'src'       => FCPATH . 'assets/uploads/',
                'dst'       => FCPATH . 'assets/backup/',
                'filename'  => 'backup_file_'.date('Y_m_d_h_i')
            ];
            $this->load->library('Rzip',$conf);
            $this->rzip->compress();
        }
    }

    function create_file_budget($tahun="") {

        $tahun1 = $tahun ;
        $tahun0 = $tahun - 1 ;
    
        $res = get_data('information_schema.tables',[
            'select' => 'table_name',
            'where' => [
                'table_schema' => 'manex',     
                'table_name REGEXP' => '_[0-9]{4}$',
                'table_name like' => "%_" . $tahun0,
                '__m' => "(substr(table_name, 1, 4) != 'act_' AND substr(table_name, 1, 4) != 'sim_')"
 
            ]
        ])->result();

        $jum = 0;
        $old_table = '';
        $new_table = '';
        foreach($res as $v) {
            $old_table = ($v->table_name);
            $new_table = str_replace($tahun0, $tahun1, $old_table);

            $sql = "CREATE TABLE $new_table LIKE $old_table";

            if(!table_exists($new_table)) {
                $this->db->query($sql);
                $jum++;
            }
        }

        if ($jum > 0) echo "Berhasil create $jum table";
    }

    function create_file_simulasi_budget($tahun="") {

        $tahun1 = $tahun ;
        $tahun0 = $tahun - 1 ;
    
        $res = get_data('information_schema.tables',[
            'select' => 'distinct table_name',
            'where' => [
                'table_schema' => 'manex',     
                'table_name REGEXP' => '_[0-9]{4}$',
                'table_name like' => '%_'. $tahun0,
                '__m' => "(substr(table_name, 1, 4) != 'act_' AND substr(table_name, 1, 4) != 'sim_')"
            ]
        ])->result();
        
        $jum = 0;
        $old_table = '';
        $new_table = '';
        foreach($res as $v) {
            $old_table = ($v->table_name);
            $new_table = 'sim_'. str_replace($tahun0, $tahun1, $old_table);;
            $sql = "CREATE TABLE $new_table LIKE $old_table";

            if(!table_exists($new_table)) {
                $this->db->query($sql);
                $jum++;
            }
        }

        if ($jum > 0) echo "Berhasil create $jum table";
    }

    function create_file_actual_budget($tahun="") {

        $tahun1 = $tahun ;
        $tahun0 = $tahun + 1 ;
    
        $res = get_data('information_schema.tables',[
            'select' => 'distinct table_name',
            'where' => [
                'table_schema' => 'manex',     
                'table_name REGEXP' => '_[0-9]{4}$',
                'table_name like' => '%_'. $tahun0,
                '__m' => "(substr(table_name, 1, 4) != 'act_' AND substr(table_name, 1, 4) != 'sim_')"
            ]
        ])->result();

        $jum = 0;
        $old_table = '';
        $new_table = '';
        foreach($res as $v) {
            $old_table = ($v->table_name);
            $new_table = 'act_'. str_replace($tahun0, $tahun1, $old_table);;
            $sql = "CREATE TABLE $new_table LIKE $old_table";

            if(!table_exists($new_table)) {
                $this->db->query($sql);
                $jum++;
            }
        }

        if ($jum > 0) echo "Berhasil create $jum table";
    }

    function recalculate_sales($tahun="",$product="",$sector="") {
        recalculate_sales($tahun,$product,$sector);
        echo 'success recalculate' ;
    }
}