<?php
error_reporting(E_ALL);
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
require_once "../lib/gestordb.php";
require_once "../config.php";
//require_once "./PHPMailer/PHPMailerAutoload.php";
$sid=session_id();
if (!ini_get('session.auto_start') and empty($sid)) {session_start();}
//require_once "page.ext";
global $tipobd_totvs,$conexion_totvs;


   $resultado 		= selec_server('TOTVS_MCHV12');
	$tipobd_totvs 		= $resultado[0];
	$conexion_totvs = $resultado[1];
	
	
function sb1_sb1340(){
	global $tipobd_totvs,$conexion_totvs;

	
	$querysel = "SELECT * FROM  sb1010 where D_E_L_E_T_<>'*'  AND B1_COD not in (SELECT B1_COD FROM SB1340@LK_MCHV11 WHERE D_E_L_E_T_<>'*') and b1_cod not in ('ANDREA','MARCELO')";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	$i = 0;
	while($v = ver_result($rss, $tipobd_totvs)){
		$i=$i+1;
		
		$b1_filial 	= $v["B1_FILIAL"];	$b1_cod 	= TRIM($v["B1_COD"]);		$b1_desc 	= $v["B1_DESC"]; 	$b1_tipo 	= $v["B1_TIPO"];		$b1_codite 	= $v["B1_CODITE"];	$b1_um 		= $v["B1_UM"];//
		$b1_locpad 	= $v["B1_LOCPAD"];	$b1_grupo 	= $v["B1_GRUPO"];	$b1_picm 	= $v["B1_PICM"]; 	$b1_ipi 	= $v["B1_IPI"];			$b1_posipi 	= $v["B1_POSIPI"];	$b1_especie = $v["B1_ESPECIE"];//
		$b1_ex_ncm 	= $v["B1_EX_NCM"];	$b1_ex_nbm 	= $v["B1_EX_NBM"];	$b1_aliqiss = $v["B1_ALIQISS"];	$b1_codiss	= TRIM($v["B1_CODISS"]);	$b1_te		= $v["B1_TE"];		$b1_ts		= $v["B1_TS"];//
		$b1_picmret	= $v["B1_PICMRET"];	$b1_picment	= $v["B1_PICMENT"];	$b1_impzfrc	= $v["B1_IMPZFRC"];	$b1_bitmap	= $v["B1_BITMAP"];	$b1_segum	= $v["B1_SEGUM"];	$b1_conv	= $v["B1_CONV"];//
		$b1_tipconv	= $v["B1_TIPCONV"];	$b1_alter	= trim($v["B1_ALTER"])? ' ' : 0;	$b1_qe		= $v["B1_QE"];		$b1_prv1	= $v["B1_PRV1"];	$b1_emin	= $v["B1_EMIN"];	$b1_custd	= $v["B1_CUSTD"];
		$b1_ucalstd	= $v["B1_UCALSTD"];	$b1_uprc	= $v["B1_UPRC"];	$b1_mcustd	= $v["B1_MCUSTD"];	$b1_ucom	= $v["B1_UCOM"];	$b1_peso	= $v["B1_PESO"];	$b1_estseg	= $v["B1_ESTSEG"];
		$b1_estfor	= $v["B1_ESTFOR"];	$b1_forprz	= $v["B1_FORPRZ"];	$b1_pe		= $v["B1_PE"];		$b1_tipe	= $v["B1_TIPE"];	$b1_le		= $v["B1_LE"];		$b1_lm		= $v["B1_LM"];
		$b1_conta	= $v["B1_CONTA"];	$b1_toler	= $v["B1_TOLER"];	$b1_cc		= $v["B1_CC"];		$b1_itemcc	= $v["B1_ITEMCC"];	$b1_familia	= $v["B1_FAMILIA"];	$b1_proc	= $v["B1_PROC"];
		$b1_qb		= $v["B1_QB"];		$b1_lojproc	= $v["B1_LOJPROC"];	$b1_apropri	= $v["B1_APROPRI"];	$b1_tipodec	= $v["B1_TIPODEC"];	$b1_origem	= $v["B1_ORIGEM"];	$b1_clasfis	= $v["B1_CLASFIS"];
		$b1_fantasm	= $v["B1_FANTASM"];	$b1_rastro	= $v["B1_RASTRO"];	$b1_urev	= $v["B1_UREV"];	$b1_datref	= $v["B1_DATREF"];	$b1_foraest	= $v["B1_FORAEST"];	$b1_comis	= $v["B1_COMIS"];
		$b1_mono	= $v["B1_MONO"];	$b1_dtrefp1	= $v["B1_DTREFP1"];	$b1_perinv	= $v["B1_PERINV"];	$b1_grtrib	= $v["B1_GRTRIB"];	$b1_mrp		= $v["B1_MRP"];		$b1_prvalid	= $v["B1_PRVALID"];
		$b1_notamin	= $v["B1_NOTAMIN"];	$b1_numcop	= $v["B1_NUMCOP"];	$b1_contsoc	= $v["B1_CONTSOC"];	$b1_conini	= $v["B1_CONINI"];	$b1_irrf	= $v["B1_IRRF"];	$b1_codbar	= trim($v["B1_CODBAR"]);
		$b1_grade	= $v["B1_GRADE"];	$b1_formlot	= $v["B1_FORMLOT"];	$b1_fpcod	= trim($v["B1_FPCOD"])? ' ' : 0;	$b1_localiz	= $v["B1_LOCALIZ"];	$b1_operpad	= $v["B1_OPERPAD"];	$b1_contrat	= $v["B1_CONTRAT"];
		$b1_desc_p	= $v["B1_DESC_P"];	$b1_desc_i	= $v["B1_DESC_I"];	$b1_desc_gi	= $v["B1_DESC_GI"];	$b1_vlrefus	= $v["B1_VLREFUS"];	$b1_import	= $v["B1_IMPORT"];	$b1_anuente	= $v["B1_ANUENTE"];
		$b1_opc		= $v["B1_OPC"];		$b1_codobs	= $v["B1_CODOBS"];	$b1_sitprod	= $v["B1_SITPROD"];	$b1_fabric	= $v["B1_FABRIC"];	$b1_modelo	= $v["B1_MODELO"];	$b1_setor	= $v["B1_SETOR"];
		$b1_balanca	= $v["B1_BALANCA"];	$b1_tecla	= $v["B1_TECLA"];	$b1_prodpai	= $v["B1_PRODPAI"];	$b1_tipocq	= $v["B1_TIPOCQ"];	$b1_solicit	= $v["B1_SOLICIT"];	$b1_grupcom	= $v["B1_GRUPCOM"];
		$b1_despimp	= $v["B1_DESPIMP"];	$b1_desbse3	= $v["B1_DESBSE3"];	$b1_quadpro	= $v["B1_QUADPRO"];	$b1_agregcu	= $v["B1_AGREGCU"];	$b1_base3	= $v["B1_BASE3"];	$b1_numcqpr	= $v["B1_NUMCQPR"];
		$b1_contcqp	= $v["B1_CONTCQP"];	$b1_revatu	= $v["B1_REVATU"];	$b1_inss	= $v["B1_INSS"];	$b1_codemb	= trim($v["B1_CODEMB"])? ' ' : 0;	$b1_especif	= $v["B1_ESPECIF"];	$b1_mat_pri	= $v["B1_MAT_PRI"];
		$b1_redinss	= $v["B1_REDINSS"];	$b1_nalncca	= $v["B1_NALNCCA"];	$b1_concgan	= $v["B1_CONCGAN"];	$b1_conciva	= $v["B1_CONCIVA"];	$b1_redirrf	= $v["B1_REDIRRF"];	$b1_aladi	= $v["B1_ALADI"];
		$b1_nalsh	= $v["B1_NALSH"];	$b1_tab_ipi	= $v["B1_TAB_IPI"];	$b1_grudes	= $v["B1_GRUDES"];	$b1_redpis	= $v["B1_REDPIS"];	$b1_redcof	= $v["B1_REDCOF"];	$b1_datasub	= $v["B1_DATASUB"];
		$b1_pcsll	= $v["B1_PCSLL"];	$b1_pcofins	= $v["B1_PCOFINS"];	$b1_ppis	= $v["B1_PPIS"];	$b1_mtbf	= $v["B1_MTBF"];	$b1_mttr	= $v["B1_MTTR"];	$b1_flagsug	= $v["B1_FLAGSUG"];
		$b1_classve	= $v["B1_CLASSVE"];	$b1_midia	= $v["B1_MIDIA"];	$b1_qtmidia	= $v["B1_QTMIDIA"];	$b1_envobr	= $v["B1_ENVOBR"];	$b1_qtdser	= $v["B1_QTDSER"];	$b1_moeda	= $v["B1_MOEDA"];
		$b1_correc	= $v["B1_CORREC"];	$b1_serie	= $v["B1_SERIE"];	$b1_faixas	= $v["B1_FAIXAS"];	$b1_nropag	= $v["B1_NROPAG"];	$b1_isbn	= $v["B1_ISBN"];	$b1_titorig	= $v["B1_TITORIG"];
		$b1_lingua	= $v["B1_LINGUA"];	$b1_edicao	= $v["B1_EDICAO"];	$b1_obsisbn	= $v["B1_OBSISBN"];	$b1_clvl	= $v["B1_CLVL"];	$b1_ativo	= $v["B1_ATIVO"];	$b1_emax	= $v["B1_EMAX"];
		$b1_pesbru	= $v["B1_PESBRU"];	$b1_tipcar	= $v["B1_TIPCAR"];	$b1_fracper	= $v["B1_FRACPER"];	$b1_vlr_icm	= $v["B1_VLR_ICM"];	$b1_vlrselo	= $v["B1_VLRSELO"];	$b1_codnor	= $v["B1_CODNOR"];
		$b1_corpri	= $v["B1_CORPRI"];  $b1_corsec	= $v["B1_CORSEC"];	$b1_nicone	= $v["B1_NICONE"];	$b1_atrib1	= $v["B1_ATRIB1"];	$b1_contav	= $v["B1_CONTAV"];	$b1_atrib2	= $v["B1_ATRIB2"];
		$b1_contac	= $v["B1_CONTAC"];	$b1_atrib3	= $v["B1_ATRIB3"];	$b1_proveed	= $v["B1_PROVEED"];	$b1_regseq	= $v["B1_REGSEQ"];	$b1_cpotenc	= $v["B1_CPOTENC"];	$b1_marca	= $v["B1_MARCA"];
		$b1_potenci	= $v["B1_POTENCI"];	$b1_qtdacum	= $v["B1_QTDACUM"];	$b1_linea	= $v["B1_LINEA"];	$b1_qtdinic	= $v["B1_QTDINIC"];	$b1_requis	= $v["B1_REQUIS"];	$b1_composi	= $v["B1_COMPOSI"];
		$b1_lotven	= $v["B1_LOTVEN"];	$b1_ok		= $v["B1_OK"];		$b1_tempora	= $v["B1_TEMPORA"];	$b1_usafefo	= $v["B1_USAFEFO"];	$b1_iat		= $v["B1_IAT"];		$b1_maquina	= $v["B1_MAQUINA"];
		$b1_ippt	= $v["B1_IPPT"];	$b1_sittrib	= $v["B1_SITTRIB"];	$b1_valepre	= $v["B1_VALEPRE"];	$b1_pmacnut	= $v["B1_PMACNUT"];	$b1_ulmoco	= $v["B1_ULMOCO"];	$b1_umoec	= $v["B1_UMOEC"];
		$b1_gfamili	= $v["B1_GFAMILI"];	$b1_qbp		= $v["B1_QBP"];		$b1_dgfamil	= $v["B1_DGFAMIL"];	$b1_cccusto	= $v["B1_CCCUSTO"];	$b1_estprod	= $v["B1_ESTPROD"];	$b1_codproc	= $v["B1_CODPROC"];
		$b1_esptec	= $v["B1_ESPTEC"];	$b1_talla	= $v["B1_TALLA"];	$b1_msblql	= $v["B1_MSBLQL"];	$b1_codqad	= trim($v["B1_CODQAD"])? ' ' : 0;	$b1_markup	= $v["B1_MARKUP"];	$b1_pmicnut	= $v["B1_PMICNUT"];
		$b1_parcei	= $v["B1_PARCEI"];	$b1_gdodif	= $v["B1_GDODIF"];	$b1_vlcif	= $v["B1_VLCIF"];	$b1_classe	= $v["B1_CLASSE"];	$b1_gccusto	= $v["B1_GCCUSTO"];	$b1_prodsbp	= $v["B1_PRODSBP"];
		$b1_pis		= $v["B1_PIS"];		$b1_lotesbp	= $v["B1_LOTESBP"];	$b1_tipobn	= $v["B1_TIPOBN"];	$b1_uvlrc	= $v["B1_UVLRC"];	$b1_fretiss	= $v["B1_FRETISS"];	$b1_regriss	= $v["B1_REGRISS"];
		$b1_csll	= $v["B1_CSLL"];	$b1_cofins	= $v["B1_COFINS"];	$b1_garant	= $v["B1_GARANT"];	$b1_tipvec	= $v["B1_TIPVEC"];	$b1_desbse2	= $v["B1_DESBSE2"];	
		$b1_codant	= $v["B1_CODANT"]? ' ' : 0;	$b1_estrori	= $v["B1_ESTRORI"]? ' ' : 0;	$b1_color	= $v["B1_COLOR"];	$b1_base	= $v["B1_BASE"];	$b1_pergart	= $v["B1_PERGART"];	$b1_base2	= $v["B1_BASE2"];
		$b1_admin	= $v["B1_ADMIN"];	$b1_userlgi	= $v["B1_USERLGI"];	$b1_userlga	= $v["B1_USERLGA"];	$b1_coniad	= $v["B1_CONIAD"];	$b1_dtalla	= $v["B1_DTALLA"];		$d_e_l_e_t_	= $v["D_E_L_E_T_"];
		$r_e_c_n_o_	= recno_sb1340(); 	$r_e_c_d_e_l_	= $v["R_E_C_D_E_L_"];
		
		
		
		$queryin = "INSERT INTO TOTVS.SB1340@LK_MCHV11(
					B1_FILIAL, B1_COD, B1_DESC, B1_TIPO, B1_CODITE, B1_UM,
					B1_LOCPAD, B1_GRUPO, B1_PICM, B1_IPI, B1_POSIPI,B1_ESPECIE,
					B1_EX_NCM, B1_EX_NBM, B1_ALIQISS, B1_CODISS, B1_TE, B1_TS,
					B1_PICMRET, B1_PICMENT, B1_IMPZFRC, B1_BITMAP, B1_SEGUM, B1_CONV,
					B1_TIPCONV, B1_ALTER, B1_QE, B1_PRV1, B1_EMIN, B1_CUSTD,
					B1_UCALSTD, B1_UPRC, B1_MCUSTD, B1_UCOM, B1_PESO, B1_ESTSEG,
					B1_ESTFOR, B1_FORPRZ, B1_PE, B1_TIPE, B1_LE, B1_LM,
					B1_CONTA, B1_TOLER, B1_CC, B1_ITEMCC, B1_FAMILIA, B1_PROC,
					B1_QB, B1_LOJPROC, B1_APROPRI, B1_TIPODEC, B1_ORIGEM, B1_CLASFIS,
					B1_FANTASM, B1_RASTRO, B1_UREV, B1_DATREF, B1_FORAEST, B1_COMIS,
					B1_MONO, B1_DTREFP1, B1_PERINV, B1_GRTRIB, B1_MRP, B1_PRVALID,
					B1_NOTAMIN, B1_NUMCOP, B1_CONTSOC, B1_CONINI, B1_IRRF, B1_CODBAR,
					B1_GRADE, B1_FORMLOT, B1_FPCOD, B1_LOCALIZ, B1_OPERPAD, B1_CONTRAT,
					B1_DESC_P, B1_DESC_I, B1_DESC_GI, B1_VLREFUS, B1_IMPORT, B1_ANUENTE,
					B1_OPC, B1_CODOBS, B1_SITPROD, B1_FABRIC, B1_MODELO, B1_SETOR,
					B1_BALANCA, B1_TECLA, B1_PRODPAI, B1_TIPOCQ, B1_SOLICIT, B1_GRUPCOM,
					B1_DESPIMP, B1_DESBSE3, B1_QUADPRO, B1_AGREGCU, B1_BASE3, B1_NUMCQPR,
					B1_CONTCQP, B1_REVATU, B1_INSS, B1_CODEMB, B1_ESPECIF, B1_MAT_PRI,
					B1_REDINSS, B1_NALNCCA, B1_CONCGAN, B1_CONCIVA, B1_REDIRRF, B1_ALADI,
					B1_NALSH, B1_TAB_IPI, B1_GRUDES, B1_REDPIS, B1_REDCOF, B1_DATASUB,
					B1_PCSLL, B1_PCOFINS, B1_PPIS, B1_MTBF, B1_MTTR, B1_FLAGSUG,
					B1_CLASSVE, B1_MIDIA, B1_QTMIDIA, B1_ENVOBR, B1_QTDSER, B1_MOEDA,
					B1_CORREC, B1_SERIE, B1_FAIXAS, B1_NROPAG, B1_ISBN, B1_TITORIG,
					B1_LINGUA, B1_EDICAO, B1_OBSISBN, B1_CLVL, B1_ATIVO, B1_EMAX,
					B1_PESBRU, B1_TIPCAR, B1_FRACPER, B1_VLR_ICM, B1_VLRSELO, B1_CODNOR,
					B1_CORPRI, B1_CORSEC, B1_NICONE, B1_ATRIB1, B1_CONTAV, B1_ATRIB2,
					B1_CONTAC, B1_ATRIB3, B1_PROVEED, B1_REGSEQ, B1_CPOTENC, B1_MARCA,
					B1_POTENCI, B1_QTDACUM, B1_LINEA, B1_QTDINIC, B1_REQUIS, B1_COMPOSI,
					B1_LOTVEN, B1_OK, B1_TEMPORA, B1_USAFEFO, B1_IAT, B1_MAQUINA,
					B1_IPPT, B1_SITTRIB, B1_VALEPRE, B1_PMACNUT, B1_ULMOCO, B1_UMOEC,
					B1_GFAMILI, B1_QBP, B1_DGFAMIL, B1_CCCUSTO, B1_ESTPROD, B1_CODPROC,
					B1_ESPTEC, B1_TALLA, B1_MSBLQL, B1_CODQAD, B1_MARKUP, B1_PMICNUT,
					B1_PARCEI, B1_GDODIF, B1_VLCIF, B1_CLASSE, B1_GCCUSTO, B1_PRODSBP,
					B1_PIS, B1_LOTESBP, B1_TIPOBN, B1_UVLRC, B1_FRETISS, B1_REGRISS,
					B1_CSLL, B1_COFINS, B1_GARANT, B1_TIPVEC, B1_DESBSE2,
					B1_CODANT, B1_ESTRORI, B1_COLOR, B1_BASE, B1_PERGART, B1_BASE2,
					B1_ADMIN, B1_USERLGI, B1_USERLGA, B1_CONIAD, B1_DTALLA, D_E_L_E_T_,
					R_E_C_N_O_, R_E_C_D_E_L_) 
			 VALUES('$b1_filial', '$b1_cod', '$b1_desc', '$b1_tipo', '$b1_codite', '$b1_um',
					'$b1_locpad', '$b1_grupo', $b1_picm, $b1_ipi, '$b1_posipi', $b1_especie,
					'$b1_ex_ncm', '$b1_ex_nbm',$b1_aliqiss, ' ', '$b1_te', '$b1_ts',
					$b1_picmret, $b1_picment, '$b1_impzfrc', '$b1_bitmap', '$b1_segum', $b1_conv,
					'$b1_tipconv', '$b1_alter', $b1_qe, $b1_prv1, $b1_emin, $b1_custd,
					'$b1_ucalstd', $b1_uprc, '$b1_mcustd', '$b1_ucom', $b1_peso, $b1_estseg,
					'$b1_estfor', '$b1_forprz', $b1_pe, '$b1_tipe', $b1_le, $b1_lm,
					'$b1_conta', $b1_toler, '$b1_cc', '$b1_itemcc', '$b1_familia', '$b1_proc',
					$b1_qb, '$b1_lojproc', '$b1_apropri', '$b1_tipodec', '$b1_origem', '$b1_clasfis',
					'$b1_fantasm', '$b1_rastro', '$b1_urev', '$b1_datref', '$b1_foraest', $b1_comis,
					'$b1_mono', '$b1_dtrefp1', $b1_perinv, '$b1_grtrib', '$b1_mrp', $b1_prvalid,
					$b1_notamin, $b1_numcop, '$b1_contsoc', '$b1_conini', '$b1_irrf', '$b1_codbar',
					'$b1_grade', '$b1_formlot', '$b1_fpcod', '$b1_localiz', '$b1_operpad', '$b1_contrat',
					'$b1_desc_p', '$b1_desc_i', '$b1_desc_gi', $b1_vlrefus, '$b1_import', '$b1_anuente',
					'$b1_opc', '$b1_codobs', '$b1_sitprod', '$b1_fabric', '$b1_modelo', '$b1_setor',
					'$b1_balanca', '$b1_tecla', '$b1_prodpai', '$b1_tipocq', '$b1_solicit', '$b1_grupcom',
					'$b1_despimp', '$b1_desbse3', '$b1_quadpro', '$b1_agregcu', '$b1_base3', $b1_numcqpr,
					$b1_contcqp, '$b1_revatu', '$b1_inss', '$b1_codemb', '$b1_especif', '$b1_mat_pri',
					$b1_redinss, '$b1_nalncca', '$b1_concgan', '$b1_conciva', $b1_redirrf, '$b1_aladi',
					'$b1_nalsh', '$b1_tab_ipi', '$b1_grudes', $b1_redpis, $b1_redcof, '$b1_datasub',
					$b1_pcsll, $b1_pcofins, $b1_ppis, $b1_mtbf, $b1_mttr, '$b1_flagsug',
					'$b1_classve', '$b1_midia', $b1_qtmidia, '$b1_envobr', $b1_qtdser, $b1_moeda,
					'$b1_correc', '$b1_serie', $b1_faixas, $b1_nropag, '$b1_isbn', '$b1_titorig',
					'$b1_lingua', '$b1_edicao', '$b1_obsisbn', '$b1_clvl', '$b1_ativo', $b1_emax,
					$b1_pesbru, '$b1_tipcar', $b1_fracper, $b1_vlr_icm, $b1_vlrselo, '$b1_codnor',
					'$b1_corpri', '$b1_corsec', '$b1_nicone', '$b1_atrib1', '$b1_contav', '$b1_atrib2',
					'$b1_contac', '$b1_atrib3', '$b1_proveed', '$b1_regseq', '$b1_cpotenc', '$b1_marca',
					$b1_potenci, $b1_qtdacum, '$b1_linea', $b1_qtdinic, '$b1_requis', '$b1_composi',
					$b1_lotven, '$b1_ok', '$b1_tempora', '$b1_usafefo', '$b1_iat', '$b1_maquina',
					'$b1_ippt', '$b1_sittrib', '$b1_valepre', $b1_pmacnut, $b1_ulmoco, $b1_umoec,
					'$b1_gfamili', $b1_qbp, '$b1_dgfamil', '$b1_cccusto', '$b1_estprod', '$b1_codproc',
					'$b1_esptec', '$b1_talla', '$b1_msblql', '$b1_codqad', $b1_markup, $b1_pmicnut,
					'$b1_parcei', '$b1_gdodif', $b1_vlcif, '$b1_classe', '$b1_gccusto', '$b1_prodsbp',
					'$b1_pis', $b1_lotesbp, '$b1_tipobn', $b1_uvlrc, '$b1_fretiss', '$b1_regriss',
					'$b1_csll', '$b1_cofins', '$b1_garant', '$b1_tipvec', '$b1_desbse2', 
					'$b1_codant', '$b1_estrori', '$b1_color', '$b1_base', $b1_pergart, '$b1_base2',
					'$b1_admin', '$b1_userlgi', '$b1_userlga', '$b1_coniad', '$b1_dtalla', '$d_e_l_e_t_',
					$r_e_c_n_o_, $r_e_c_d_e_l_)";
				$rsi = querys($queryin, $tipobd_totvs, $conexion_totvs);
				
				echo $i." - ".$b1_cod."<br>";
					
		
	}
}
function recno_sb1340(){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT max(R_E_C_N_O_) AS RECNO FROM SB1340@LK_MCHV11";
    $rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
    $fila = ver_result($rss, $tipobd_totvs);
    if($fila["RECNO"]==null or $fila["RECNO"]==0){
        return 1;
    }else{
        return $fila["RECNO"]+1;
    }
}

sb1_sb1340();
?>