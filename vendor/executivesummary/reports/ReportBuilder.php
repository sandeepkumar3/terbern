<?php

require_once 'vendors/executivesummary/configs/config.php';

class ReportBuilder extends config
{

    /**
     * 
     * @param string $violation_type
     * $violation_type could be 'dob', 'dobecb', 'hpd', 'fdny'
     * @param int $user_id
     */
    function getViolations($violation_type, $user_id)
    {
        if ($user_id != '') {
            $arrViolations = array();
            if ($violation_type != '') {
                $violation_type = strtolower($violation_type);
                switch ($violation_type) {
                    case "dob":
                        $executeSQL = mysqli_query($this->link,
                                "CALL sp_getDOBViolations($user_id, 60)");
                        while ($eachRecord = mysqli_fetch_assoc($executeSQL)) {
                            $arrViolations[] = $eachRecord;
                        }
                        $this->reconnect_disconnect($executeSQL);
                        return $arrViolations;
                        break;
                    case "dobecb":
                        $executeSQL = mysqli_query($this->link,
                                "CALL sp_getDOBECBViolations($user_id, 60)");
                        while ($eachRecord = mysqli_fetch_assoc($executeSQL)) {
                            $arrViolations[] = $eachRecord;
                        }
                        $this->reconnect_disconnect($executeSQL);
                        return $arrViolations;
                        break;
                    case "hpd":
                        $executeSQL = mysqli_query($this->link,
                                "CALL sp_getHPDViolations($user_id, 60)");
                        while ($eachRecord = mysqli_fetch_assoc($executeSQL)) {
                            $arrViolations[] = $eachRecord;
                        }
                        $this->reconnect_disconnect($executeSQL);
                        return $arrViolations;
                        break;
                    case "fdny":
                        $executeSQL = mysqli_query($this->link,
                                "CALL sp_getFDNYViolations($user_id, 90)");
                        while ($eachRecord = mysqli_fetch_assoc($executeSQL)) {
                            $arrViolations[] = $eachRecord;
                        }
                        $this->reconnect_disconnect($executeSQL);
                        return $arrViolations;
                        break;
                }
            } else {
				$num_days = 90;
                $executeDOBSQL = mysqli_query($this->link, "CALL sp_getDOBViolations($user_id, $num_days)");
                while ($eachDOBRecord = mysqli_fetch_assoc($executeDOBSQL)) {
                    $arrViolations['dob_violations'][] = $eachDOBRecord;
                }
                $this->reconnect_disconnect($executeDOBSQL);

                $executeECBSQL = mysqli_query($this->link,
                        "CALL sp_getDOBECBViolations($user_id, $num_days)");
                while ($eachECBRecord = mysqli_fetch_assoc($executeECBSQL)) {
                    $arrViolations['ecb_violations'][] = $eachECBRecord;
                }
                $this->reconnect_disconnect($executeECBSQL);

                $executeHPDSQL = mysqli_query($this->link, "CALL sp_getHPDViolations($user_id, $num_days)");
                while ($eachHPDRecord = mysqli_fetch_assoc($executeHPDSQL)) {
                    $arrViolations['hpd_violations'][] = $eachHPDRecord;
                }
                $this->reconnect_disconnect($executeHPDSQL);

                $executeFDNYSQL = mysqli_query($this->link,
                        "CALL sp_getFDNYViolations($user_id, $num_days)");
                while ($eachFDNYRecord = mysqli_fetch_assoc($executeFDNYSQL)) {
                    $arrViolations['fdny_violations'][] = $eachFDNYRecord;
                }
                $this->reconnect_disconnect($executeFDNYSQL);
                return $arrViolations;
            }
        } else {
            return 0;
        }
    }

    /**
     * 
     * @param number $user_id
     * @param array $property_ids
     * @param number $days
     */
    function getECBHearings($user_id, $property_ids = array(), $days = 45)
    {
	$ecbdata = array();
	
        if ($user_id != '') {
            $executeSQL = mysqli_query($this->link,
                    "CALL sp_getECBHearingsAndPenalties($user_id, '', $days)");
            
            while ($eachRecord = mysqli_fetch_assoc($executeSQL)) {
            	
                $arrECBHearings[] = $eachRecord;
            }
            if(!empty($arrECBHearings)){
            	foreach ( $arrECBHearings as $key=>$ecbhearing){
            		if($ecbhearing['totalagencyhearing'] == NULL ){
            			$ecbdata['totals'] = $ecbhearing;
            		}else{
            			$total_viobyagency = explode(":", $ecbhearing['totalagencyhearing']);
            			$total_amtbyagency = explode(":", $ecbhearing['totalagencyimposedamount']);
            			$ecbdata['agency'][$total_viobyagency[0]]['total'] = $total_viobyagency['1'];
            			$ecbdata['agency'][$total_viobyagency[0]]['amt'] = $total_amtbyagency['1'];
            		}
            	}
            }
            $this->reconnect_disconnect($executeSQL);
            return $ecbdata;
        } elseif (!empty($property_ids)) {
            $arrproperties = implode(',', $property_ids);
            $executeSQL = mysqli_query($this->link,
                    "CALL sp_getECBHearingsAndPenalties('', '$arrproperties', $days)");
            while ($eachRecord = mysqli_fetch_assoc($executeSQL)) {
                $arrECBHearings[] = $eachRecord;
            }
            if(!empty($arrECBHearings)){
            	foreach ( $arrECBHearings as $key=>$ecbhearing){
            		if($key == 0){
            			$ecbdata['totals'] = $ecbhearing;
            		}else{
            			$total_viobyagency = explode(":", $ecbhearing['totalagencyhearing']);
            			$total_amtbyagency = explode(":", $ecbhearing['totalagencyimposedamount']);
            			$ecbdata['agency'][$total_viobyagency[0]]['total'] = $total_viobyagency['1'];
            			$ecbdata['agency'][$total_viobyagency[0]]['amt'] = $total_amtbyagency['1'];
            		}
            	}
            }
            $this->reconnect_disconnect($executeSQL);
            
            return $ecbdata;
        } else {
            return 0;
        }
    }

    /**
     * 
     * @param number $user_id
     * @param number $number_of_days
     */
    function getDOBComplaints($user_id = 0, $number_of_days = 90,$avg ="")
    {
        if ($user_id != '') {
        	if($avg == ''){
            $executeSQL = mysqli_query($this->link,
                    "CALL sp_getDOBComplaintStats($user_id, $number_of_days,'')");
        	}else{
        		$executeSQL = mysqli_query($this->link,
        				"CALL sp_getDOBComplaintStats($user_id, $number_of_days,$avg)");
        	}
            while ($eachRecord = mysqli_fetch_assoc($executeSQL)) {
                $arrDOBComplaints[] = $eachRecord;
            }
            $this->reconnect_disconnect($executeSQL);
            return $arrDOBComplaints;
        } else {
            return 0;
        }
    }

    /**
     * 
     * @param number $user_id
     * @param number $number_of_days
     */
    function getHPDComplaints($user_id = 0, $number_of_days = 90,$avg = '')
    {
        if ($user_id != '') {
        	if($avg == ""){
            $executeSQL = mysqli_query($this->link,
                    "CALL sp_getHPDComplaintStats($user_id, $number_of_days,'')");
        	}else{
        		$executeSQL = mysqli_query($this->link,
        				"CALL sp_getHPDComplaintStats($user_id, $number_of_days,$avg)");
        	}
            while ($eachRecord = mysqli_fetch_assoc($executeSQL)) {
                $arrHPDComplaints[] = $eachRecord;
            }
            $this->reconnect_disconnect($executeSQL);
            return $arrHPDComplaints;
        } else {
            return 0;
        }
    }

    function getTopComplaintConditions($user_id = 0, $number_of_days = 30, $limit = 5)
    {
    	
        $arrHPDTopComplaint = array();
        $total = 0;
		$donotfor90days = true;
        if ($user_id != '') {
            $cconditions = array();
            $pconditions = array();
            $tcconditions = array();
            $executeSQL = mysqli_query($this->link,
                    "CALL sp_getHPDTopComplaintConditions($user_id, $number_of_days,$limit)");
            while ($eachRecord = mysqli_fetch_assoc($executeSQL)) {
            	$complaint_totals = explode(',', $eachRecord['complaint_total']);
                $complaint_conditions = explode(',', $eachRecord['complaint_condition']);
                $complaint_percent = explode(',', $eachRecord['%']);
                foreach ($complaint_totals as $complaint_total) {
                	if (!empty($complaint_total)) {
                		$tconditions = split(":", $complaint_total);
                		$tcconditions[trim($tconditions[0])] = $tconditions['1'];
                		if($donotfor90days === true && $tconditions['1'] >= 10 && $number_of_days == 30 ){
                			echo $conditions['1']."<br/>";
                			$donotfor90days = false;
                		}
                	}
                }
                
                
                foreach ($complaint_conditions as $complaint_tanents) {
                    if (!empty($complaint_tanents)) {
                        $conditions = split(":", $complaint_tanents);
                        $cconditions[trim($conditions[0])] = $conditions['1'];
                    }
                }
                foreach ($complaint_percent as $percent_tanents) {
                    if (!empty($percent_tanents)) {
                        $pcondition = split(":", $percent_tanents);
                        $pconditions[trim($pcondition[0])] = $pcondition['1'];
                    }
                }


                $arrHPDTopComplaint['conditions'] = $tcconditions;
                $arrHPDTopComplaint['percentages'] = $pconditions;
                $arrHPDTopComplaint['numdays'] = $number_of_days;
                
            }
            $this->reconnect_disconnect($executeSQL);
            if($donotfor90days === true && $number_of_days == 30){ 
            	$arrHPDTopComplaint['recall90'] = 1 ;
            }
            return $arrHPDTopComplaint;
        } else {
            return 0;
        }
    }

    /**
     * 
     * @param number $user_id
     * @param number $year
     */
    function getElevatorInspectionStats($user_id = 0, $year = 0)
    {
        if ($user_id != '') {
            $executeSQL = mysqli_query($this->link,
                    "CALL sp_getElevatorInspectionStats($user_id, $year)");
            while ($eachRecord = mysqli_fetch_assoc($executeSQL)) {
                $arrElevatorInspections[] = $eachRecord;
            }
            $this->reconnect_disconnect($executeSQL);
            return $arrElevatorInspections;
        } else {
            return 0;
        }
    }

    /**
     * 
     * @param number $user_id
     * @param number $year
     */
    function getBoilerInspectionStats($user_id = 0)
    {
        if ($user_id != '') {
            $executeSQL = mysqli_query($this->link,
                    "CALL sp_getBoilerInspectionStats($user_id)");
            while ($eachRecord = mysqli_fetch_assoc($executeSQL)) {
                $arrBoilerInspections[] = $eachRecord;
            }
            $this->reconnect_disconnect($executeSQL);
            return $arrBoilerInspections;
        } else {
            return 0;
        }
    }

    /**
     * 
     * @param int $user_id
     * @param int $year
     */
    function getFacadeInspectionStats($user_id = 0, $year = 0)
    {
        if ($user_id != '') {
            $executeSQL = mysqli_query($this->link,
                    "CALL sp_getFacadeInspectionStats($user_id, '".$year."')");
            while ($eachRecord = mysqli_fetch_assoc($executeSQL)) {
                $arrFacadeInspections[] = $eachRecord;
            }
            $this->reconnect_disconnect($executeSQL);
            return $arrFacadeInspections;
        } else {
            return 0;
        }
    }

    /**
     * 
     * @param int $user_id
     */
    function getCriticalIssues($user_id = 0)
    {

        if ($user_id != '') {
            $arrInspections = array();
            $executeElevatorInspectionSQL = mysqli_query($this->link,
                    "CALL sp_getElevatorInspectionFilings($user_id, '')");
            while ($eachElevatorInspectionRecord = mysqli_fetch_assoc($executeElevatorInspectionSQL)) {
                $arrInspections['elevator_inspections'][] = $eachElevatorInspectionRecord;
            }
            $this->reconnect_disconnect($executeElevatorInspectionSQL);

            $executeFacadeInspectionSQL = mysqli_query($this->link,
                    "CALL sp_getFacadeInspectionFilings($user_id, '')");
            while ($eachFacadeInspectionRecord = mysqli_fetch_assoc($executeFacadeInspectionSQL)) {
                $arrInspections['facade_inspections'][] = $eachFacadeInspectionRecord;
            }
            $this->reconnect_disconnect($executeFacadeInspectionSQL);

            // class 1
            /* $executeHPDSQL = mysqli_query($this->link,"CALL sp_getHPDViolations($user_id, 60)");
              while ($eachHPDRecord = mysqli_fetch_assoc($executeHPDSQL)) {
              $arrViolations['hpd_violations'][] = $eachHPDRecord;
              }
             */

            $executeSWOSQL = mysqli_query($this->link, "CALL sp_getSWO($user_id, '')");
            while ($eachSWORecord = mysqli_fetch_assoc($executeSWOSQL)) {
                $arrInspections['stop_workorders'][] = $eachSWORecord;
            }
            $this->reconnect_disconnect($executeSWOSQL);

            $executeVacateOrdersQL = mysqli_query($this->link,
                    "CALL sp_getVacateOrders($user_id, '')");
            while ($eachVacateOrdersRecord = mysqli_fetch_assoc($executeVacateOrdersQL)) {
                $arrInspections['vacate_orders'][] = $eachVacateOrdersRecord;
            }
            $this->reconnect_disconnect($executeVacateOrdersQL);

            $executeERPSQL = mysqli_query($this->link, "CALL sp_getERP($user_id, '')");
            while ($eachERPRecord = mysqli_fetch_assoc($executeERPSQL)) {
                $arrInspections['erps'][] = $eachERPRecord;
            }
            $this->reconnect_disconnect($executeERPSQL);

            $executeHPDPendingLitigationSQL = mysqli_query($this->link,
                    "CALL sp_getHPDPendingLitigation($user_id, '')");
            while ($eachHPDPendingLitigationRecord = mysqli_fetch_assoc($executeHPDPendingLitigationSQL)) {
                $arrInspections['hpd_litigations'][] = $eachHPDPendingLitigationRecord;
            }
            $this->reconnect_disconnect($executeHPDPendingLitigationSQL);
			
            $noncomplaintclasses =  mysqli_query($this->link, "CALL sp_getDOBECBnoncompliantclass($user_id)");
            while ($eachnoncomplaintclassesRecord = mysqli_fetch_assoc($noncomplaintclasses)) {
            	$arrInspections['noncomplaintclass'][] = $eachnoncomplaintclassesRecord;
            }
            $this->reconnect_disconnect($noncomplaintclasses);
            return $arrInspections;
        } else {
            return array();
        }
    }

    /**
     * 
     */
    function getViolationByDays($violation_type, $user_id, $days)
    {
        if ($user_id != '') {
            $arrViolations = array();
            $violation_type = strtolower($violation_type);
            switch ($violation_type) {
                case "dob":
                    $executeSQL = mysqli_query($this->link,
                            "CALL sp_getDOBViolations($user_id, $days)");
                    while ($eachRecord = mysqli_fetch_assoc($executeSQL)) {
                        $arrViolations[] = $eachRecord;
                    }
                    $this->reconnect_disconnect($executeSQL);
                    return $arrViolations;
                    break;
                case "dobecb":
                    $executeSQL = mysqli_query($this->link,
                            "CALL sp_getDOBECBViolations($user_id, $days)");
                    while ($eachRecord = mysqli_fetch_assoc($executeSQL)) {
                        $arrViolations[] = $eachRecord;
                    }
                    $this->reconnect_disconnect($executeSQL);
                    return $arrViolations;
                    break;
                case "hpd":
                    $executeSQL = mysqli_query($this->link,
                            "CALL sp_getHPDViolations($user_id, $days)");
                    while ($eachRecord = mysqli_fetch_assoc($executeSQL)) {
                        $arrViolations[] = $eachRecord;
                    }
                    $this->reconnect_disconnect($executeSQL);
                    return $arrViolations;
                    break;
                case "fdny":
                    $executeSQL = mysqli_query($this->link,
                            "CALL sp_getFDNYViolations($user_id, $days)");
                    while ($eachRecord = mysqli_fetch_assoc($executeSQL)) {
                        $arrViolations[] = $eachRecord;
                    }
                    $this->reconnect_disconnect($executeSQL);
                    return $arrViolations;
                    break;
            }
        } else {
            return 0;
        }
    }

}

?>