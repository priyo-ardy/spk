<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class VwSPK extends Migration
{
    public function up()
    {
        $this->db->query("DROP VIEW IF EXISTS vw_t_spk");
        $this->db->query("
            CREATE VIEW vw_t_spk AS
            SELECT
                A.id,
                A.kategori,
                CASE
                    WHEN A.kategori = '1' THEN 'SPK Mold'
                    WHEN A.kategori = '2' THEN 'SPK Mesin'
                    WHEN A.kategori = '3' THEN 'SPK Preventive Request'
                    WHEN A.kategori = '4' THEN 'SPK Equipment Request'
                END AS nama_kategori,
                A.code,
                A.lokasi,
                B.name AS nama_lokasi,
                A.dept,
                C.name AS nama_dept,
                A.pelapor,
                D.NIK,
                D.nama AS nama_karyawan,
                A.tgl_lapor,
                A.mold_confirm,
                A.planner_confirm,
                A.mold_finish,
                A.quality_confirm,
                A.material,
                CASE
                    WHEN A.kategori = '1' THEN E_material.code
                    WHEN A.kategori = '2' THEN E_mesin.code
                END AS kode_material,
                CASE
                    WHEN A.kategori = '1' THEN E_material.name
                    WHEN A.kategori = '2' THEN E_mesin.name
                END AS nama_material,
                CASE
                    WHEN A.kategori = '1' THEN E_material.model
                    WHEN A.kategori = '2' THEN NULL -- atau kolom yang sesuai dari m_mesin
                END AS model_material,
                A.nomor_mesin,
                A.leader,
                F.NIK AS nik_leader,
                F.nama AS nama_leader,
                A.defect,
                G.name AS nama_defect,
                A.sub_defect,
                H.name AS nama_sub_defect,
                A.berulang,
                CASE
                    WHEN A.berulang = '0' THEN 'No'
                    WHEN A.berulang = '1' THEN 'Yes'
                END AS nama_berulang,
                A.prioritas,
                CASE
                    WHEN A.prioritas = '0' THEN 'Low'
                    WHEN A.prioritas = '1' THEN 'Normal'
                    WHEN A.prioritas = '2' THEN 'Urgent'
                    WHEN A.prioritas = '3' THEN 'Critical'
                END AS nama_prioritas,
                A.tipe_equipment,
                CASE
                    WHEN A.tipe_equipment = '1' THEN 'Machine Equipment'
                    WHEN A.tipe_equipment = '2' THEN 'Transportation Equipment'
                    WHEN A.tipe_equipment = '3' THEN 'Final Inspection Equipment'
                    WHEN A.tipe_equipment = '4' THEN 'Laboratorium Equipment'
                    WHEN A.tipe_equipment = '5' THEN 'Electronic Equipment'
                    WHEN A.tipe_equipment = '6' THEN 'Other Equipment'
                END AS nama_tipe_equipment,
                A.alasan_repair,
                J.name AS nama_alasan_repair,
                A.deskripsi,
                CASE
                    WHEN A.dokumen_status = '0' THEN 'Created'
                    WHEN A.dokumen_status = '1' THEN 'Submitted'
                    WHEN A.dokumen_status = '2' THEN 'Approve'
                    WHEN A.dokumen_status = '3' THEN 'On progress in Mold'
                    WHEN A.dokumen_status = '4' THEN 'On progress in Planner'
                    WHEN A.dokumen_status = '5' THEN 'On progress in Quality'
                    WHEN A.dokumen_status = '6' THEN 'Hold'
                    WHEN A.dokumen_status = '7' THEN 'Reject'
                    WHEN A.dokumen_status = '8' THEN 'Close'
                    WHEN A.dokumen_status = '9' THEN 'Re-Approved'
                END AS nama_dokumen_status,
                A.flow_status,
                CASE
                    WHEN A.flow_status = '0' THEN 'Waiting mold confirm'
                	WHEN A.flow_status = '1' THEN 'Waiting planner confirm'
                    WHEN A.flow_status = '2' THEN 'Waiting mold engineer finish'
                    WHEN A.flow_status = '3' THEN 'ME Confirm'
                    WHEN A.flow_status = '4' THEN 'Waiting quality confirm'
                    WHEN A.flow_status = '5' THEN 'ME Finish'
                    WHEN A.flow_status = '6' THEN 'Quality Confirm'
                    WHEN A.flow_status = '7' THEN 'Close'
                END AS 'nama_flow',
                A.level_status,
                CASE
                	WHEN A.level_status = '0' THEN 'Low'
                    WHEN A.level_status = '1' THEN 'Normal'
                    WHEN A.level_status = '2' THEN 'Urgent'
                    WHEN A.level_status = '3' THEN 'Critical'
                END AS 'nama_level',
                CASE
                	WHEN A.lokasi_repair = '1' then 'Internal Repair'
                	WHEN A.lokasi_repair = '2' then 'External Repair'
                END AS nama_lokasi_repair,
                A.jig_status,
                CASE
                    WHEN A.jig_status = '0' THEN 'Before SOP'
                    WHEN A.jig_status = '1' THEN 'After SOP'
                END AS nama_jig_status,
                A.supplier,
                K.name  AS nama_supplier,
                A.appearance,
                CASE
                	WHEN A.appearance = 0 then 'NG'
                	WHEN A.appearance = 1 then 'OK'
                	WHEN A.appearance = 2 then 'No Check'
                END AS nama_appearance,
                A.dimension,
                CASE
                	WHEN A.dimension = 0 then 'NG'
                	WHEN A.dimension = 1 then 'OK'
                	WHEN A.dimension = 2 then 'No Check'
                END AS nama_dimension,
                A.performance,
                CASE 
                	WHEN A.performance = 0 then 'NG'
                	WHEN A.performance = 1 then 'OK'
                	WHEN A.performance = 2 then 'No Check'
                END AS nama_performance,
                A.created_at,
                A.created_by,
                A.updated_at,
                A.updated_by,
                A.deleted_at
            FROM t_spk AS A
            	LEFT JOIN m_lokasi AS B ON A.lokasi = B.id
	            LEFT JOIN m_dept AS C ON A.dept = C.id
	            LEFT JOIN m_karyawan AS D ON A.pelapor = D.id
    	        LEFT JOIN m_material AS E_material ON A.kategori = '1' AND A.material = E_material.id
        	    LEFT JOIN m_mesin AS E_mesin ON A.kategori = '2' AND A.material = E_mesin.id
            	LEFT JOIN m_leader AS F ON A.leader = F.id
            	LEFT JOIN m_defect AS G ON A.defect = G.id
	            LEFT JOIN m_sub_defect AS H ON A.sub_defect = H.id
	            LEFT JOIN m_repair AS J ON A.alasan_repair = J.id
	            left join m_supplier AS K on A.supplier = K.id
            ORDER BY A.tgl_lapor DESC;
        ");
    }

    public function down()
    {
        $this->db->query("DROP VIEW IF EXISTS vw_t_spk");
    }
}
