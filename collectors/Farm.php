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

class Farm extends Collector
{
	protected $idx;
    static protected $aObjects = [];
	
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
                if(isset($aArray) && is_array($aArray) && count($aArray) > 0) 
                {
                    $sDefaultOrg = Utils::GetConfigurationValue('org', '');
                    foreach($aArray as $aObject)
                    {
                        self::$aObjects[] = array(
                            'id' => $aObject['datacenter']['id']."-".$aObject['datacenter']['name'],
                            'name' => $aObject['datacenter']['name'],
                            'org_id' => $sDefaultOrg
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
			);
		}
		return false;
	}
}