<?php
if(!defined('BASEPATH')) exit('No Direct Access allowed');

class Snmp {
  
  private $_host= null;

  public function getHostDetails($ip) {

    $this->_host['system Description']= $this->_systemDescription($ip); // call private function to poppulate these fields
    $this->_host['uptime']= $this->_upTime($ip);
    // get others too
    $this->_host['system Time']= $this->_systemTime($ip);

    $this->_host['cpu Utilization']= $this->_cpuUtilization($ip);

    $this->_host['storage type']= $this->_storageType($ip);

    $this->_host['storage space']= $this->_storageSpace($ip);

    $this->_host['disk Type']= $this->_diskType ($ip);

    $this->_host['disk FreeSpace']= $this->_diskFreeSpace($ip);

    $this->_host['installed Softwares']= $this->_installedSoftwares($ip);

    $this->_host['services types']= $this->_serviceTypes($ip);

    $this->_host['services']= $this->_services($ip);
    return $this->_host;
  }
  
  private function _cpuUtilization($ip) {

  	snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
	$cpuUtilization = @snmpwalk($ip, "public",".1.3.6.1.2.1.25.3.3.1.2");
	if($cpuUtilization != NULL) {
		$i = 0;
		foreach ($cpuUtilization as $value) {
			$cpuUtilization[$i] = $value." "."%";
			$i++;
		}
	}	
	return $cpuUtilization; 
  }
  
  private function _upTime($ip) {

	snmp_set_valueretrieval(SNMP_VALUE_PLAIN);	
	$upTime = @snmpwalk($ip, "public",".1.3.6.1.2.1.25.1.1"); 
	if($upTime != NULL) {
		foreach ($upTime as $value) {
			$hr = floatval(($value/(100))/3600);
			$min = floatval((($value/100)%3600)/60);
			$sec = floatval((($value/100)%3600)%60);
			$upTime = number_format(floor($hr))."hr ".":".number_format(floor($min))."min ".":".number_format(ceil($sec))."sec ";
		}
	}
	return $upTime;
  }
  
  private function _storageSpace($ip) {

  	
	$storageSize = array();
	$storageUsed = array();
	$memory = array();
	snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
	$allocationUnit = @snmpwalk($ip, "public",".1.3.6.1.2.1.25.2.3.1.4"); 
	$size = @snmpwalk($ip, "public",".1.3.6.1.2.1.25.2.3.1.5"); 
	$used = @snmpwalk($ip, "public",".1.3.6.1.2.1.25.2.3.1.6"); 
	if($allocationUnit != NULL) {
		$i = 0;
		foreach ($allocationUnit as $value) {
			$stSize[$i]=$allocationUnit[$i]*$size[$i];
			$sizeInGb=number_format($stSize[$i]/(1024*1024*1024),2).'GB';
			$storageSize[$i]=$sizeInGb;
			
			$stUsed[$i]=$allocationUnit[$i]*$used[$i];
			$usedInGb=number_format($stUsed[$i]/(1024*1024*1024),2).'GB';
			$storageUsed[$i]=$usedInGb;
			$i++;
			$memory['size']=$storageSize;
			$memory['used space']=$storageUsed;	
		}	
	}
	return $memory;
    
  }
  
  private function _storageType($ip) {

  	snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
	$storage = @snmpwalk($ip, "public",".1.3.6.1.2.1.25.2.3.1.3");
	return $storage;
  }
  
  private function _diskFreeSpace($ip) {
  	
  	$diskFreeSpace = array();
	snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
	$diskFreeSpace = @snmpwalk($ip, "public",".1.3.6.1.2.1.25.3.6.1.4");
	if($diskFreeSpace != NULL) {
		$i=0;
		foreach ($diskFreeSpace as $value) {
			$diskFreeSpace[$i]=number_format($value/(1024*1024),2).'GB';
			$i++;	
		}
	}
	return $diskFreeSpace;  
  }
  
  private function _diskType ($ip) {
    
    $diskType = array();
	snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
	$diskType = @snmpwalk($ip, "public",".1.3.6.1.2.1.25.3.6.1.2");
	return $diskType;
  }
  
  private function _systemTime($ip) {
    
    $systemTime = array();
	snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
	$systemTime = @snmpwalk($ip, "public", ".1.3.6.1.2.1.25.1.2");
	return $systemTime[0];
  }
  
  private function _systemDescription($ip) {
    
    $systemDescription = array();
	snmp_set_quick_print(1);
	$systemDesc = @snmpwalk($ip, "public", ".1.3.6.1.2.1.1.1");
	if($systemDesc != NULL) { 
		foreach ($systemDesc as $value) {
			$systemExplode=explode(" ", $value);
			$os=preg_grep('/^(Windows|Linux)/', $systemExplode);
			$osString=implode("", $os);
			preg_match('/^[a-zA-z:\s0-9]+/', $value,$matches);
			$systemDescription['os']=$osString;
			$systemDescription['descripyion']=$matches[0];
		}
	}
	return $systemDescription;
  }
  
  private function _installedSoftwares($ip) {
	
	$installedSoftwares = array();
	snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
	$installedSoftwares = @snmpwalk($ip, "public", ".1.3.6.1.2.1.25.6.3.1.2");
	sort($installedSoftwares);
	$i = 0;
	foreach ($installedSoftwares as $value) {
		$trim = trim($value,'"\"');
		$installedSoftwares[$i] = $trim;
		$i++;
	}
	return  $installedSoftwares;
}
  
  private function _services($ip) {
   
    $services = array();
    snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
	$services = @snmpwalk($ip, "public", ".1.3.6.1.2.1.25.4.2.1.2");
	$i = 0;
	foreach ($services as $value) {
		$trim = trim($value,'"\"');
		$services[$i] = $trim;
		$i++;
	}
	return array_count_values($services);
  }
  
  private function _serviceTypes($ip) {
   
    $serviceTypes = array();
    snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);	
	$serviceTypes = @snmpwalk($ip, "public", ".1.3.6.1.2.1.25.4.2.1.6");
	return array_count_values($serviceTypes);
  }

}