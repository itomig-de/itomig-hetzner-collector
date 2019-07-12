<?php

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'collector.hetzner.itomig/0.9.1',
	array(
		// Identification
		//
		'label' => 'Hetzner Cloud API Collector (ITOMIG GmbH)',
		'category' => 'collector',

		// Setup
		//
		'dependencies' => array(
		),
		'mandatory' => false,
		'visible' => true,

		// Components
		//
		'datamodel' => array(
		),
		'webservice' => array(
			
		),
		'data.struct' => array(
			// add your 'structure' definition XML files here,
		),
		'data.sample' => array(
			// add your sample data XML files here,
		),
		
		// Documentation
		//
		'doc.manual_setup' => '', // hyperlink to manual setup documentation, if any
		'doc.more_information' => '', // hyperlink to more information, if any 

		// Default settings
		//
		'settings' => array(
			// Module specific settings go here, if any
		),
	)
);
