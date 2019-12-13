<?php

namespace FWatchDog;



use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\Player;
use pocketmine\utils\Config;

class Commands extends Command
{


	public function onCommand(CommandSender $sender, Command $cmd, $label, array $arg)
	{
		
		switch(strtolower($cmd->getName()))
		{
		case "hack":
			
			if(!$sender instanceof Player)//判断是否为玩家
			{
				
				$sender->sendMessage("请在游戏内使用这个指令");
				break;
			}
			$sender->sendMessage("§b-----FWatchDog Commands Helper-----");
			$sender->sendMessage("§c§l1./DogInfo §eTo see the WatchDog version");
			$sender->sendMessage("§c§l2./DogReload §eTo Update the WatchDog");
			$sender->sendMessage("§dFillAmeaPixel NetWork");
			break;
		case "doginfo":
			
			$sender->sendMessage("§bWatchDog Version");
			$sender->sendMessage("§bWatchDog Version: 1.1.3");
			$sender->sendMessage("§bWatchDog api 3.0.0");
			$sender->sendMessage("§bWatchDog正在§c{$this->server->getName()}上运行");
			break;
		}
		
		return true;//告诉服务器这个命令使用方法正确
	}
	
	
	
	}