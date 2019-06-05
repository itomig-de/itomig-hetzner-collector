<?php
// Copyright (C) 2014-2015 Combodo SARL
//
//   This application is free software; you can redistribute it and/or modify	
//   it under the terms of the GNU Affero General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or
//   (at your option) any later version.
//
//   iTop is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU Affero General Public License for more details.
//
//   You should have received a copy of the GNU Affero General Public License
//   along with this application. If not, see <http://www.gnu.org/licenses/>

require_once APPROOT.'extensions/Client.class.php';

class VirtualMachine extends Collector
{
	protected $idx;
    static protected $aObjects = [];
    
    static protected $oStatus;
    static protected $oOSFamily;
	
	public function AttributeIsOptional($sAttCode)
	{
		// If the module Service Management for Service Providers is selected during the setup
		// there is no "services_list" attribute on VirtualMachines. Let's safely ignore it.
		return parent::AttributeIsOptional($sAttCode);
	}
	
	static public function GetServers()
	{
        try
        {
            $aTokens = Utils::GetConfigurationValue('hetzner_tokens', array());

            if (!is_array($aTokens) || count($aTokens) < 1)
            {
                return self::$aObjects;
            } 

            foreach ($aTokens as $sToken)
            {
                $oClient = new Client($sToken);
                $aResult = $oClient->server->list();

                $aArray = $aResult['servers'];
                if(isset($aArray) && is_array($aArray) && count($aArray) > 0) {
                    $sDefaultOrg = Utils::GetConfigurationValue('org', '');
                    $sDefaultStatus = Utils::GetConfigurationValue('default_status', '');

                    foreach($aArray as $aObject)
                    {
                        $dCreationDate = date("Y-m-d", strtotime($aObject['image']['created']));
                        self::$aObjects[] = array(
                            'id' => $aObject['id']." - ".$aObject['name']." - ".$aObject['datacenter']['name']. " - ".$dCreationDate,
                            'name' => $aObject['name'],
                            'org_id' => $sDefaultOrg,
                            'status' => self::$oStatus->MapValue($aObject['status'], $sDefaultStatus),
                            'virtualhost_id' => $aObject['datacenter']['name'],
                            'osfamily_id' => self::$oOSFamily->MapValue($aObject['image']['os_flavor']),
                            'osversion_id' => $aObject['image']['os_version'],
                            'move2production' => $dCreationDate,
                            'managementip' => $aObject['public_net']['ipv4']['ip'],
                        );
                    }
                }
            }
            return self::$aObjects;
        } catch (Exception $e)
        {
            Utils::Log(LOG_ERROR, 'Error : '.$e->getMessage());
            echo "error : ".$e->getMessage();
        }
	}
	
	public function Prepare()
	{
		$bRet = parent::Prepare();
		if (!$bRet) return false;

        self::$oStatus = new MappingTable('status');
        self::$oOSFamily = new MappingTable('os_family');
		self::GetServers();

		$this->idx = 0;
		return true;
	}

	public function Fetch()
	{
		if ($this->idx < count(self::$aObjects))
		{
			$aObject = self::$aObjects[$this->idx++];
			return array(
					'primary_key' => $aObject['id'],
                    'name' => $aObject['name'],
                    'org_id' => $aObject['org_id'],
                    'status' => $aObject['status'],
                    'virtualhost_id' => $aObject['virtualhost_id'],
                    'osfamily_id' => $aObject['osfamily_id'],
                    'osversion_id' => $aObject['osversion_id'],
                    'move2production' => $aObject['move2production'],
                    'managementip' => $aObject['managementip'],
			);
		}
		return false;
	}
}