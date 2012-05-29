<?php
	define('VER', '0.29 alpha');
	echo "TDLive Yap'pm " . VER . "\nA FULL (alpha) PHP package manager!\n(c)2012 TDLive Inc.\nThis TEST release is licensed under the Creative Commons BY-NC-SA license, available at:\nhttp://creativecommons.org/licenses/by-nc-sa/3.0/\n";
	if(@$argv[1] == "install"){
		if(! @isset($argv[2])){
			echo "I won't know which package to install unless you tell me!";
			exit;
		}
		$repos=Array("http://localhost/yappm");
		echo "Beginning to look for '" . $argv[2] . "'...\n";
		foreach($repos as $value){
			if(!@isset($found)){
			echo "Checking the Yap'pm repo '$value/'...\n";
			echo "Downloading the Yap'pm repo packagelist for '$value/'...\n";
			if(! $packages=@file_get_contents("$value/packagelist.inf")){ echo "Could not reach $value/. Check your Internet connection.\n"; $connected=false; } else{ $connected=true; }
			if($connected){
				$value2=$value;
				echo "Checking the package list for the selected package in '$value/'...\n";
				$packages=explode("\r\n", $packages);
				foreach($packages as $value){
					echo "Looking for " . $argv[2] . "...\n";
					if($argv[2] == $value){
						$found=true;
						$value=$value2;
						$pkg=$argv[2];
					}
				}
			}
			}
		}
		if(! @isset($found)){
			echo "The package you selected does not exist.";
			exit;
		}
		if($found){
			echo "Package found in '$value2/'!\n";
			echo "Getting package info for '$pkg'...\n";
			if(! $pkginfo=@file_get_contents("$value2/packages/$pkg/info.inf")) { die("O noes! The package info was not found!"); }
			echo "Doing a little thinking...\n";
			$pkginfo=explode("\r\n", $pkginfo);
			$package["id"]=$argv[2];
			$package["name"]=$pkginfo[0];
			$package["desc"]=$pkginfo[1];
			echo "You are about to install " . $package["id"] . ":\n";
			echo $package["name"] . "\n";
			echo $package["desc"] . "\n";
			echo "Are you OK with this? [Y/N] (Y): ";
			$operation=fgets(STDIN);
			$operation=str_replace("\r\n", "", $operation);
			$operation=str_replace("\n", "", $operation);
			$operation=strtolower($operation);
			if($operation == "n"){
				die("Aborted!");
			}
			else{
				echo "Preparing to install " . $package["name"] . "...\n";
				echo "Reading installation data from '$value2/packages/$pkg/install.inf'...\n";
				if(! $installinfo=@file_get_contents("$value2/packages/$pkg/install.inf")) { die("O noes! The package install info was not found!"); }
				$installinfo=explode("\r\n", $installinfo);
				foreach($installinfo as $value){
					$file=explode(" ", $value);
					$unames=strtolower(php_uname('s'));
					$cwd=getcwd();
					$directories_to_make=explode("/", $file[1]);
					if(isset($directories_to_make)){
						foreach($directories_to_make as $value){
							echo $value;
							mkdir($value);
							chdir($value);
						}
					}
					file_put_contents($file[0], file_get_contents($value2 . "/packages/$pkg/files/" . $file[0]) or die("Could not find file " . $file[0] . "."));
				}
			}
		}
	}
	else{
		echo "'I have no idea what you are talking about.' ~Lisa Lutz";
		exit;
	}
?>