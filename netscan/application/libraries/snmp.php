<?php if(!defined('BASEPATH')) exit('No Direct Access allowed');

class Snmp {
	private $_host= null;

	/**
	*Call functions
	*
	*@param $ip
	*@return $_host array
	*@access public
	*/

	public function getHostDetails($ip) {

		$this->_host = $this->_systemDescription($ip); 

		if($ip == "localhost") {
			$this->_host['ip'] = $this->_serverIp($ip); 
		}
		
		$this->_host['deviceType'] = $this->_deviceType($ip);
		
		$this->_host['computerName'] = $this->_computerName($ip);

		$this->_host['uptime'] = $this->_upTime($ip);

		$this->_host['systemTime']= $this->_systemTime($ip);

		$this->_host['cpuUtilization']= $this->_cpuUtilization($ip);

		$this->_host['routers'] = $this->_router($ip);

		$this->_host['storageDevices']= $this->_storageType($ip);
	
		$this->_host['bandwidthUtilization']= $this->_bandwidth($ip);

		$this->_host['diskType']= $this->_diskType ($ip);

		$this->_host['installedSoftwares']= $this->_installedSoftwares($ip);

		$this->_host['services']= $this->_services($ip);

		$this->_host['interfaces'] = $this->_interfaces($ip);

		$this->_host['ports'] = $this->_tcpPorts($ip);

		return $this->_host;
		
	} //function ends
	
	/**
	*Get server IP
	*
	*@param $ip
	*@return string
	*@access private
	*/
	private function _serverIp($ip) {
		$ip = @snmpwalk($ip, "public",".1.3.6.1.2.1.4.20.1.1","8000");
		return $ip[1];
	}

	/**
	*Get computer name of each host
	*
	*@param $ip
	*@return string
	*@access private
	*/
	private function _computerName($ip) {
		snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
		$name = @snmpwalk($ip, "public",".1.3.6.1.2.1.1.5","8000");
		if($name != NULL) {
			foreach ($name as $key => $value) {
			$name = $value;
			}
			return $name;
		}
		
	}

	/**
	*Get CPU utilization of each host
	*
	*@param $ip
	*@return array
	*@access private
	*/
	private function _cpuUtilization($ip) {
	  	snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
	  	$cpu = array();
		$cpuUtilization = @snmpwalk($ip, "public",".1.3.6.1.2.1.25.3.3.1.2","9000");
		$cpuDesc = @snmpwalk($ip, "public",".1.3.6.1.2.1.25.3.2.1.3","9000");
		if($cpuUtilization != NULL) {
			$i = 0;
			foreach ($cpuUtilization as $value) {
				$cpuUtilization[$i] = $value;
				$i++;
			}
			$cpu['average'] = array_sum($cpuUtilization)/count($cpuUtilization);
			$cpu['noOfProcessors'] = count($cpuUtilization);
			$cpu['processors'] = $cpuDesc[3];
			return $cpu; 
		}
		else {
			return $cpu = array('average' => null,'noOfProcessors' => null,'processors' => null );
		}
    } //function ends

    /**
	*Get uptime of each host
	*
	*@param $ip
	*@return string
	*@access private
	*/
    private function _upTime($ip) {
		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);	
		$upTime = @snmpwalk($ip, "public",".1.3.6.1.2.1.25.1.1","8000"); 
		if($upTime != NULL) {
			foreach ($upTime as $value) {
				$hr = floatval(($value/(100))/3600);
				$min = floatval((($value/100)%3600)/60);
				$sec = floatval((($value/100)%3600)%60);
				$upTime = number_format(floor($hr))."hr ".":".number_format(floor($min))."min ".":".number_format(ceil($sec))."sec ";
			}
			return $upTime;
		}
	} //function ends

	/**
	*Get system time of each host
	*
	*@param $ip
	*@return string
	*@access private
	*/
	private function _systemTime($ip) {
    
	    $systemTime = array();
		snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
		$systemTime = @snmpwalk($ip, "public", ".1.3.6.1.2.1.25.1.2","8000");
		return $systemTime[0];
	}
  	
  	/**
	*Get description of each host
	*
	*@param $ip
	*@return array
	*@access private
	*/
	private function _systemDescription($ip) {

		$systemDescription = array();
		snmp_set_quick_print(1);
		$systemDesc = @snmpwalk($ip, "public", ".1.3.6.1.2.1.1.1","9000");
		if($systemDesc != NULL) { 
			foreach ($systemDesc as $value) {
				$systemExplode=explode(" ", $value);
				$os=preg_grep('/^(Windows|Linux)/', $systemExplode);
				$osString=implode("", $os);
				preg_match('/^[a-zA-z:\s0-9]+/', $value,$matches);
				$systemDescription['os']=$osString;
				$systemDescription['description']=$matches[0];
			}
			return $systemDescription;
		}
		else {
			$systemDescription['os']=NULL;
			$systemDescription['description'] =NULL;
			return $systemDescription;
		}
	}

	/**
	*Get disk type of each host
	*
	*@param $ip
	*@return array
	*@access private
	*/
	private function _diskType ($ip) {

		$diskType = array();
		$disk = array();
		$diskFreeSpace = $this->_diskStorageCapacity($ip);
		snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
		$diskType = @snmpwalk($ip, "public",".1.3.6.1.2.1.25.3.6.1.2","9000");
		if($diskType != NULL) {
			for ($i=0; $i < count($diskType) ; $i++) { 
				$disk['type'][$i] = $diskType[$i];
			}
			$disk['freeSpace'] = $diskFreeSpace;
			return $disk;
		}
	}

	/**
	*Get all software installed in each host
	*
	*@param $ip
	*@return array
	*@access private
	*/
	private function _installedSoftwares($ip) {
	
		$softwares = array();
		$installedSoftwares = NULL;
		snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
		$softwares['index'] = @snmpwalk($ip, "public", ".1.3.6.1.2.1.25.6.3.1.1","8000");
		$softwares['name'] = @snmpwalk($ip, "public", ".1.3.6.1.2.1.25.6.3.1.2","8000");
		$softwares['type'] = @snmpwalk($ip, "public", ".1.3.6.1.2.1.25.6.3.1.4","8000");
		$softwares['installtionDate'] = @snmpwalk($ip, "public", ".1.3.6.1.2.1.25.6.3.1.5","8000");
		if($softwares['name'] != NULL) {
			sort($softwares['name']);
		}
			for ($i=0; $i < count($softwares['index']) ; $i++) { 
				$trim = trim($softwares['name'][$i],'"\"');
				$installedSoftwares[$i] = array('name' => $trim, 'type' => $softwares['type'][$i], 'installationDate' => $softwares['installtionDate'][$i]);
				}	
			return  $installedSoftwares;
	}

	/**
	*Get storage space of each drive
	*
	*@param $ip
	*@return array
	*@access private
	*/
	private function _storageSpace($ip) {
		$storageSize = array();
		$storageUsed = array();
		$memory = array();
		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
		$allocationUnit = @snmpwalk($ip, "public",".1.3.6.1.2.1.25.2.3.1.4","8000"); 
		$size = @snmpwalk($ip, "public",".1.3.6.1.2.1.25.2.3.1.5","8000"); 
		$used = @snmpwalk($ip, "public",".1.3.6.1.2.1.25.2.3.1.6","8000"); 
		if($allocationUnit != NULL) {
			$i = 0;
			foreach ($allocationUnit as $value) {
				$stSize[$i]=$allocationUnit[$i]*$size[$i];
				$sizeInGb=number_format($stSize[$i]/(1024*1024*1024),2);
				$storageSize[$i] = $sizeInGb;			
				
				$stUsed[$i] = $allocationUnit[$i]*$used[$i];
				$usedInGb = number_format($stUsed[$i]/(1024*1024*1024),2);
				$storageUsed[$i] = $usedInGb;

				$i++;
				$memory['size'] = $storageSize;
				$memory['usedSpace'] = $storageUsed;	
			}	
			return $memory;
		}
		else {
			return $memory = array('size' => null,'usedSpace' => null);
		}
		
    } //function ends

    /**
	*Get type of drive of each host
	*
	*@param $ip
	*@return array
	*@access private
	*/
    private function _storageType($ip) {
	  	snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
	  	$space = $this->_storageSpace($ip);
	  	$diskSpace = $this->_totalDiskSpace($ip);
	  	$store['total'] = $diskSpace; 
		$storage = @snmpwalk($ip, "public",".1.3.6.1.2.1.25.2.3.1.3","8000");
		if ($storage !=NULL) {
			$grep = preg_grep('/(C:|D:|E:|F:|G:|[virtual]| [physical])/', $storage);
			$i=0;
			foreach ($grep as $key => $value) {
				$data = preg_split('/[:\s]+/', $value);
				$store['description'][$data[0]] = array('total' => $space['size'][$i] ,'used' => $space['usedSpace'][$i] );
				$i++; 	
			}
			return $store;
		}
		else {
			return $store = array('total'=> $diskSpace,'description' => array('Physical' => array('total' => null,'used' => null )));
		}
    } //function ends

    /**
	*Get storage space of disk
	*
	*@param $ip
	*@return array
	*@access private
	*/
    private function _diskStorageCapacity($ip) {
	  	$diskFreeSpace = array();
		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
		$diskFreeSpace = @snmpwalk($ip, "public",".1.3.6.1.2.1.25.3.6.1.4","8000");
		if($diskFreeSpace != NULL) {
			$i=0;
			foreach ($diskFreeSpace as $value) {
				$diskFreeSpace[$i]=number_format($value/(1024*1024),2);
				$i++;	
			} 
			return $diskFreeSpace;
		}  
    } //function ends

    /**
	*Get all services running in each host
	*
	*@param $ip
	*@return array
	*@access private
	*/
	private function _services($ip) {
		$services = array();
		snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
		$services['index'] = @snmpwalk($ip, "public", ".1.3.6.1.2.1.25.4.2.1.1","8000");
		$services['name'] = @snmpwalk($ip, "public", ".1.3.6.1.2.1.25.4.2.1.2","8000");
		$services['types'] = @snmpwalk($ip, "public", ".1.3.6.1.2.1.25.4.2.1.6","8000");
		$services['status'] = @snmpwalk($ip, "public", ".1.3.6.1.2.1.25.4.2.1.7","8000");
		$services['cpuPerf'] = @snmpwalk($ip, "public", ".1.3.6.1.2.1.25.5.1.1.1","8000");
		$services['memPerf'] = @snmpwalk($ip, "public", ".1.3.6.1.2.1.25.5.1.1.2","8000");
		for ($i=0; $i <count($services['index']) ; $i++) { 
			$cpu = $services['cpuPerf'][$i]/100;
			$cpu = $cpu." "."sec";
			$trim = trim($services['name'][$i],'"\"');
			$serv[$i] = array('index' => $services['index'][$i],'name' => $trim, 'types' => $services['types'][$i], 'status' =>$services['status'][$i], 'cpuPerf' => $cpu, 'memPerf' => $services['memPerf'][$i]);
		}
		$serv['noOfServices'] = count($services['index']);
		return $serv;
	}
    
    /**
	*Get total memory in each host
	*
	*@param $ip
	*@return array
	*@access private
	*/
	private function _totalMemory($ip) {
		$totalMemory = array();
		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
		$totalMemory = @snmpwalk($ip, "public",".1.3.6.1.2.1.25.2.2","8000");
		if ($totalMemory != NULL) {
		foreach ($totalMemory as $key => $value) {
			$totalMemory = round(number_format($value/(1024*1024),2));
			}
			return $totalMemory;
		}
	}

	/**
	*Get total disk space in each host
	*
	*@param $ip
	*@return array
	*@access private
	*/
	private function _totalDiskSpace($ip) {
		$storageSize = array();
		$totalDisk['size'] = NULL;
		$totalDisk['used'] = NULL;
		snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
		$allocationUnit = @snmpwalk($ip, "public",".1.3.6.1.2.1.25.2.3.1.4","8000"); 
		$size = @snmpwalk($ip, "public",".1.3.6.1.2.1.25.2.3.1.5","8000"); 
		$used = @snmpwalk($ip, "public",".1.3.6.1.2.1.25.2.3.1.6","8000");
		if($allocationUnit != NULL) {
			$i = 0;
			foreach ($allocationUnit as $value) {
				$stSize[$i]=$allocationUnit[$i]*$size[$i];
				$sizeInGb=number_format($stSize[$i]/(1024*1024*1024),2);
				$storageSize[$i] = $sizeInGb;
				$stUsed[$i]=$allocationUnit[$i]*$used[$i];
				$usedInGb=number_format($stUsed[$i]/(1024*1024*1024),2);
				$storageUsed[$i] = $usedInGb;			
				$i++;
			}	
			$j = 0;
			while ($allocationUnit[$j] != 0) {
				$totalDisk['size']+=$storageSize[$j];
				$totalDisk['used']+=$storageUsed[$j];
				$j++;
			//	$totalDisk['size'] = $totalDisk['size'];	
			//	$totalDisk['used'] = $totalDisk['used'];	
			}
		return $totalDisk;
		}
		else {
			return $totalDisk = array('size' => null,'used' => null);
		}
	}

	/**
	*Get all inforamtion of interfaces in each host
	*
	*@param $ip
	*@return array
	*@access private
	*/
	private function _interfaces($ip) {
		$data['ifindex'] = @snmpwalk($ip, "public", "1.3.6.1.2.1.2.2.1.1","8000"); 
		$data['ifname'] = @snmpwalk($ip, "public", "1.3.6.1.2.1.2.2.1.2","8000");
		$data['iftype'] = @snmpwalk($ip, "public", "1.3.6.1.2.1.2.2.1.3","8000");
		$data['ifmtu'] = @snmpwalk($ip, "public", "1.3.6.1.2.1.2.2.1.4","8000");
		$data['ifspeed'] = @snmpwalk($ip, "public", "1.3.6.1.2.1.2.2.1.5","8000");
		$data['ifphysicaladdr'] = @snmpwalk($ip, "public", "1.3.6.1.2.1.2.2.1.6","8000");
		$data['ifopstatus'] = @snmpwalk($ip, "public", "1.3.6.1.2.1.2.2.1.8","8000");
		$data['ifAlias'] = @snmpwalk($ip, "public", "1.3.6.1.2.1.31.1.1.1.18","8000");
		for ($i=0; $i < count($data['ifname']) ; $i++) { 
				$if[$i] = array('index' => $data['ifindex'][$i],'name' => $data['ifname'][$i], 'type' => $data['iftype'][$i], 'mtu' => $data['ifmtu'][$i], 'speed' => $data['ifspeed'][$i] , 'physicaladdress' => $data['ifphysicaladdr'][$i], 'operationstatus' => $data['ifopstatus'][$i] ,'alias' => $data['ifAlias'][$i] );
		}
		return $if;

	}

	/**
	*Get tcp ports of each host
	*
	*@param $ip
	*@return array
	*@access private
	*/
	private function _tcpPorts($ip) {
		$data['udp'] = $this->_udpPorts($ip);
		$data['tcp']['activeOpen'] = @snmpwalk($ip, "public", "1.3.6.1.2.1.6.5","8000");
		$data['tcp']['passiveOpen'] = @snmpwalk($ip, "public", "1.3.6.1.2.1.6.6","8000");
		$data['tcp']['attemptFail'] = @snmpwalk($ip, "public", "1.3.6.1.2.1.6.7","8000");
		$data['tcp']['connStatus'] = @snmpwalk($ip, "public", "1.3.6.1.2.1.6.13.1.1","8000");
		$data['tcp']['localPorts'] = @snmpwalk($ip, "public", "1.3.6.1.2.1.6.13.1.3","8000");
		$data['tcp']['remotePorts'] = @snmpwalk($ip, "public", "1.3.6.1.2.1.6.13.1.5","8000"); 
		return $data;	
	}

	/**
	*Get udp ports of each host
	*
	*@param $ip
	*@return array
	*@access private
	*/
	private function _udpPorts($ip) {
		$data['udpPorts'] = @snmpwalk($ip, "public", "1.3.6.1.2.1.7.5.1.2","8000");
		$data['udpnoports'] = @snmpwalk($ip, "public", "1.3.6.1.2.1.7.2","8000");
		return $data;
	}
	
	/**
	*Get type of device i.e workstation or server
	*
	*@param $ip
	*@return array
	*@access private
	*/
	private function _deviceType($ip) {
		$device = array();
		snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
		$data = @snmpwalk($ip, "public", "1.3.6.1.4.1.77.1.1.3","9000");
		if($data != NULL) {
			foreach ($data as $key => $value) {
				$trim = trim($value,'"\"');
				if($trim == "00 00 00 00 ") {
					$value1 = "Workstation";
				}
				elseif ($trim == "01 00 00 00 ") {
					$value1 = "Server";
				}
				elseif ($trim == "02 00 00 00 ") {
					$value1 = "Sql Server";
				}
				elseif ($trim == "03 00 00 00 ") {
					$value1 = "Primary Domain Controller";
				}
				elseif ($trim == "04 00 00 00 ") {
					$value1 = "Backup Doamin Controller";
				}
				elseif ($trim == "05 00 00 00 ") {
					$value1 = "Time source";
				}
				elseif ($trim == "06 00 00 00 ") {
					$value1 = "AFP Server";
				}
				elseif ($trim == "07 00 00 00 ") {
					$value1 = "Netware Server";
				}
				$device = array('type' => $value1,'count' => count($value1) );
			}
			return $device;
		}
		else {
			return $device = array('type' => null,'count' => null);
		}
	}

	/**
	*Detect if device is router or not
	*
	*@param $ip
	*@return count of routers
	*@access private
	*/
	private function _router($ip) {
		snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
		$value = NULL;
		$data = @snmpwalk($ip, "public", "1.3.6.1.2.1.4.1","8000");
		if($data != NULL) {
			if(!in_array("notForwarding", $data)) {
				$value = $data;
			}
			return count($value);
		}
	}

	/**
	*Get bandwodth utilization of each host
	*
	*@param $ip
	*@return string
	*@access private
	*/
	private function _bandwidth($ip) {
		$bandwidth = NULL;
		$in = NULL;
		$out = NULL;
		$sp = NULL;
		$inoctet = @snmpwalk($ip, "public", "1.3.6.1.2.1.2.2.1.10","9000");
		$outoctet = @snmpwalk($ip, "public", "1.3.6.1.2.1.2.2.1.16","9000");
		$speed = @snmpwalk($ip, "public", "1.3.6.1.2.1.2.2.1.5","9000");
		if($inoctet !=NULL || $outoctet !=NULL || $speed !=NULL) {
			if(is_array($inoctet) && is_array($outoctet) && is_array($speed)){
				$in = max($inoctet);
				$out = max($outoctet);
				$sp = max($speed);
			}
			if ($sp != NULL) {
				$bandwidth = (($in+$out)*8*100)/$sp;
			}
			return $bandwidth;
		}
	}
	
} // class end
  