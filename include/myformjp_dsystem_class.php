<?php
if( !defined('ABSPATH') ) exit;
class myformjp_dsystem {
	var $ver= "1.13";
	var $dba="";
	var $logn="";                                 // debug/_xxxxx.txt
	var $target="";
	var $fields="";
function myformjp_dsystem_base(){
	$wk= explode(".", basename($_SERVER['PHP_SELF'])); // xxxxx.php
	$this->logn= "debug/_".$wk[0].".txt";         // debug/_xxxxx.txt
}
function myformjp_DBpath() {return myformjp_UPS;}
function myformjp_DBopen(){
	if( $dba=="" ){                               // 未open
		$fname = $this->myformjp_DBpath();
		$this->dba= new SQLite3($fname);
	}
	return $this->dba;
}
function myformjp_DBclose(){
	if( $dba!="" ){                               // open済み
		$this->dba->close();
		$this->dba= "";
	}
}
function myformjp_get_target(){return $this->target;}
function myformjp_set_target($target){$this->target=$target;}
function myformjp_sql($sql){
	$res= $this->dba->query($sql);
	if( $res==false ) exit("[DB処理エラー]_".$sql."##<br>");
	return $res;
}
function myformjp_get_field() {return $this->fields;}
function myformjp_set_field($tablen){
 	$this->myformjp_DBopen();
	$this->fields= $this->myformjp_DBshow($tablen); // 項目一覧
}
function myformjp_DBshow($table){
	$list= array();
	$sql= "PRAGMA table_info(". $table .")";
	$res= $this->myformjp_sql($sql);
	if( $res ){
		while( $rows= $res->fetchArray(SQLITE3_ASSOC) ) {$list[]=(object)$rows;}
	}
	return $list;
}
function myformjp_DBall($table,$where="",$order="",$limit="",$offset="",$opt="") {
	return $this->myformjp_DBsql("select * FROM $table",$where,$order,$limit,$offset,$opt);
}
function myformjp_DBsql($sqlc, $where="",$order="",$limit="",$offset="",$opt="") {
	$list= array();
	$sql= $sqlc;
	if( $where != "" ) $sql.= " WHERE $where";
	if( $order != "" ) $sql.= " ORDER BY $order";
	if( $limit != "" ) $sql.= " LIMIT $limit";
	if( $offset!= "" ) $sql.= " OFFSET $offset";
	$res= $this->myformjp_sql($sql);
//echo $sql."##<br>";
	while( $rows= $res->fetchArray(SQLITE3_ASSOC) ) {
		if($opt=="1")$list[]=        $rows;
		else         $list[]=(object)$rows;
	}
	return $list;
}
function myformjp_DBisrt($tablen,$array) {        // array -> insert
	$sql= $value= "";
	if( !is_array($array) ) $array= array();
	foreach($array as $key => $data){
		if( $key == "id" ) {;}                    // idはskip
		else {
			if( !empty($sql) ) {$sql.=","; $value.=",";}
			$sql.= '"'.$key.'"';
			if( strncmp($data,"datetime(",9)==0 ) $value.= $data;
			else  $value.= '"'.$data.'"';
	}	}
	$sql= "insert into ".$tablen."(".$sql.") values(".$value.")";
	$res= $this->myformjp_sql($sql);
}
function myformjp_DBupdt($tablen,$array, $where="") { // array -> update
	$sql= "";
	if( !is_array($array) ) $array= array();
	foreach($array as $key => $data){
		if( !empty($sql) ) $sql.= ",";
		$sql.= '"'.$key.'"=';
		if( strncmp($data,"datetime(",9)==0 ) $sql.= $data;
		else  $sql.= '"'.$data.'"';
	}
	$sql= "UPDATE $tablen SET ". $sql;
	if( $where != "" ) $sql.= " WHERE $where";
	$res= $this->myformjp_sql($sql);
}
}
$dbio= new myformjp_dsystem;
$dbio->myformjp_dsystem_base();
?>
