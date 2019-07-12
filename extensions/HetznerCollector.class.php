<?php

require_once APPROOT.'extensions/Client.class.php';

class HetznerCollector extends Collector
{
	protected $idx;
    protected $aObjects = [];
	
	public function AttributeIsOptional($sAttCode)
	{
		// If the module Service Management for Service Providers is selected during the setup
		// there is no "services_list" attribute on VirtualMachines. Let's safely ignore it.
		return parent::AttributeIsOptional($sAttCode);
	}
    
    public function GetData()
    {
        try
        {
            $aTokens = Utils::GetConfigurationValue('hetzner_tokens', array());

            // verify that there are tokens in the configuration, has to be a minimum of one token
            if (!is_array($aTokens) || count($aTokens) < 1)
            {
                return $this->aObjects;
            } 

            // fill the object array with each token
            foreach ($aTokens as $sToken)
            {
                $oClient = new Client($sToken);
                $aResult = $oClient->server->list();
    
                // get json array from hetzner with all data
                $aArray = $aResult;
                // if the returned array is not empty
                if(isset($aArray) && is_array($aArray) && count($aArray) > 0)
                {
                    foreach($aArray as $aObject)
                    {
                        $this->aObjects[] = $this->ExtractData($aObject);
                    }
                }
            }
            return $this->aObjects;
        } catch (Exception $e)
        {
            Utils::Log(LOG_ERROR, 'Error : '.$e->getMessage());
            echo "error : ".$e->getMessage();
        }
    }

    /**
     * extracts the necessary infos from the given json array node
     */
    public function ExtractData($aObject)
    {
        echo "test\n";
        return array();
    }

    /**
     * initialize constants like organization name
     */
    public function InitConstants()
    {
        //
    }

    public function PrepareMappingTables()
    {
        //
    }
	
	public function Prepare()
	{
		$bRet = parent::Prepare();
		if (!$bRet) return false;

        $this->PrepareMappingTables();
        $this->InitConstants();
		$this->GetData();

		$this->idx = 0;
		return true;
	}

	public function Fetch()
	{
		if ($this->idx < count($this->aObjects))
		{
            $val = $this->aObjects[$this->idx++];
			return $val;
		}
		return false;
	}
}