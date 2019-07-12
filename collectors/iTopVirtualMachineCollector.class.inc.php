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

class iTopVirtualMachineCollector extends HetznerCollector
{
	protected $idx;
    protected $aObjects = [];
    
    protected $oStatus;
    protected $oOSFamily;

    protected $bHasTeemIp = null;
    protected $sDefaultStatus;
    protected $sDefaultOrg;

    protected function ExtractData($aObject)
    {
        $dCreationDate = date("Y-m-d", strtotime($aObject['image']['created']));
        return array(
            'primary_key' => $aObject['id']."-".$aObject['name']."-".$aObject['datacenter']['name']. "-".$dCreationDate,
            'name' => $aObject['name'],
            'org_id' => $this->sDefaultOrg,
            'status' => $this->oStatus->MapValue($aObject['status'], $this->sDefaultStatus),
            'virtualhost_id' => $aObject['datacenter']['name'],
            'osfamily_id' => $this->oOSFamily->MapValue($aObject['image']['os_flavor']), // TODO: Lucie : should be collected ???
            'osversion_id' => $aObject['image']['os_version'], // TODO: Lucie : collect it too, create when does not exist
            'move2production' => $dCreationDate,
            'managementip' => $aObject['public_net']['ipv4']['ip'], // TODO: Lucie : check modules for teemip, different behaviour
        );
    }

    protected function InitConstants()
    {
        $this->sDefaultOrg = Utils::GetConfigurationValue('org', '');
        $this->sDefaultStatus = Utils::GetConfigurationValue('default_status', '');
    }

    protected function PrepareMappingTables()
    {
        $this->oStatus = new MappingTable('status');
        $this->oOSFamily = new MappingTable('os_family');
    }
}