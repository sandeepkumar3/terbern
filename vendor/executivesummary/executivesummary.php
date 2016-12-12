<?php

require_once 'vendors/executivesummary/api/User.php';
require_once 'vendors/executivesummary/reports/ReportBuilder.php';
require_once 'vendors/executivesummary/reports/ReportConfig.php';

class executivesummary{
	var $exuser;
	var $exreportbuilder;
        var $exreportconfig;
	
	function __construct(){
		$this->exuser = new Users();
		$this->exreportbuilder = new ReportBuilder();
                $this->exreportconfig = new ReportConfig();
	}
	
	/**
	 * 
	 * @param number $userid
	 */
	function getinfo($userId = 0){
		$data = array();
		$data['userflag'] = $userflags = $this->exuser->getUserFlags($userId);
		$data['userinfo'] = $userStatus = $this->exuser->getUserStatus($userId);
		$data['buildingcount'] = $propertycount = $this->exuser->getUserPropertyCount($userId);
		return $data;
	}
	
	/**
	 * 
	 * @param number $user_id
	 */
	function get_usersummaryrecord($user_id = 0){
		$data = array();
		$data['summary'] = $this->exreportbuilder->getViolations('', $user_id);
		
		// ECB Hearings Section
		$data['ECBHearings'] = $this->exreportbuilder->getECBHearings($user_id);
		$data['DOBComplaints90'] = $this->exreportbuilder->getDOBComplaints($user_id);
		$data['HPDComplaints90'] = $this->exreportbuilder->getHPDComplaints($user_id);
		$data['DOBComplaints365'] = $this->exreportbuilder->getDOBComplaints($user_id,365,1);
		$data['HPDComplaints365'] = $this->exreportbuilder->getHPDComplaints($user_id,365,1);
		$data['HPDTopCoplaints'] = $this->exreportbuilder->getTopComplaintConditions($user_id);
		if($data['HPDTopCoplaints']['recall90'] == 1){
			$data['HPDTopCoplaints'] = $this->exreportbuilder->getTopComplaintConditions($user_id,90);
		}
		// Critical Issues Section
		$data['CriticalIssues'] = $this->exreportbuilder->getCriticalIssues($user_id);
		// inspection inprogress
		$data['Elevatorinspectionstats'] = $this->exreportbuilder->getElevatorInspectionStats($user_id,1);
		$data['Boilerinspectionstats'] = $this->exreportbuilder->getBoilerInspectionStats($user_id);
		$data['FacadeInspectionStats'] = $this->exreportbuilder->getFacadeInspectionStats($user_id,'');
		return $data;
	}
	
}