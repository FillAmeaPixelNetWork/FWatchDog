<?php

namespace FWatchDog;

use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\Server;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageByEntity;
use pocketmine\event\entity\EntityDamageEvent;

use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\entity\PrimedTNT;


use pocketmine\math\Vector3;
use pocketmine\utils\MainLogger;

use pocketmine\block\Block;
use pocketmine\entity\Entity;

use pocketmine\level\Level;

use pocketmine\event\player\PlayerHungerChangeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\event\player\PlayerDeathEvent;


use pocketmine\event\Event;
use pocketmine\event\EventPriority;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\player\PlayerChatEvent;

use pocketmine\entity\Snowball;
use pocketmine\level\Position;


//Player\PlayerDropItemEvent
use pocketmine\event\player\PlayerDropItemEvent;




use FWatchDog\CreativeBlocks;
use FWatchDog\CriticalEvent;
use FWatchDog\Kernel;
use FWatchDog\Commamds;

class FWatchDog extends PluginBase implements Listener {

public $prefix = "§c§l[FWatchDog]§e";
public $players= array();
public $move=array();
public $time=array();
	//public $settime = array();
	public $messaget = array();
	/** @var Array */
	private $order;
	
	public function __construct(){
		$this->order = array();
	}	
	
const DEFAULT_COMPATIBLE_KERNELS = ["SteadFast", "Genisys", "PocketMine-MP", "Altay", "GenisysPro", "FillAmeaPixel-FillAmeaPixel", "FillAmeaPixel-FillAmeaPixel"];
	const NORMAL_PRE     = "FWatchDog";
	const PLUGIN_PREFIX  = "§c§l[FWatchDog]§e";
	const API_VERSION    = "3.0.0";
	
	
	
	private $server = null, $mypath = null, $config = null, $playerlog = null;
	private static $instance      = null;
	private $taskManager          = null; 
	private $commandManager       = null; 
	private $pluginManager        = null; 
	private $eventManager         = null; 
	private $interface_plugin     = [];   
	private static $player_packet = [];
	private $default_interface_plugin_data = 
	[
		"name"      => "",
		"author"    => "",
		"version"   => "",
		"api"       => [],
		"commands"  => [],
		"object"    => null
	];	
	
	
	
	
	
	
	
public function onEnable() {
	$this->getServer()->getPluginManager()->registerEvents($this, $this);
	$this->hack();
	$this->Kernel();
	
	
	if(!is_dir($this->getDataFolder()))
			mkdir($this->getDataFolder());
		$s = new CreativeBlocks($this->getDataFolder()."Blocks.sqlite3",$this);
		$this->sql = $s;	
}


//AntiHack
   public function hack(){
    	/*
		@mkdir($this->getDataFolder());
 		$this->cfg=new Config($this->getDataFolder()."config.yml",Config::YAML,array());
		if(!$this->cfg->exists("Message-delay"))
		{
			$this->cfg->set("Message-delay","1.5");
			$this->cfg->save();
		}
		$this->settime=$this->cfg->get("Message-delay");
		*/
    }




//No Hungry Plus
public function onHunger(PlayerHungerChangeEvent $e){
$e->setCancelled();
}

//No Create PVP

public function onEntityDamageByEntity(EntityDamageEvent $event){
        $entity = $event->getEntity();
        if($event instanceof EntityDamageByEntityEvent){
            $damager = $event->getDamager();
            if($entity instanceof Player && $damager instanceof Player){
			if($damager->isCreative()==true){
			 $event->setCancelled(true);
             $damager->sendMessage("§c§l[FWatchDog]§b自动判定§e: Please Do not use the hack. §dType:Create ");
			}
			}
		}
}

	public function onBreak(BlockBreakEvent $event){
		$player = $event->getPlayer();
		if(!$event->isCancelled()){
			$block = $event->getBlock();
			$x = $block->getX();
			$y = $block->getY();
			$z = $block->getZ();
			$level = $block->getLevel()->getName();
			if($this->sql->iscreativeblock($x, $y, $z, $level)){
				$this->sql->delblock($x, $y, $z, $level);
				if(!$player->isCreative()){
					$event->setDrops(array());
					$player->sendTip($this->prefix.TextFormat::RED."§eDo not have create Block §bType:Brush");
				}
			}
		}
	}
	
	public function onPlace(BlockPlaceEvent $event){
		$player = $event->getPlayer();
		if(!$event->isCancelled()){
			if($player->isCreative()){
				$block = $event->getBlock();
				$x = $block->getX();
				$y = $block->getY();
				$z = $block->getZ();
				$level = $block->getLevel()->getName();
				$this->sql->add($x, $y, $z, $level);
			}
		}
	}
	
	//AntiHack
	
	 public function onMove(PlayerMoveEvent $event){

if($event->getPlayer() instanceof Player and $event->getPlayer()->getGamemode() !== 1){
           $player = $event->getPlayer();
$nx=$player->getX();
$ny=$player->getY();
$nz=$player->getZ();
if(!isset($this->players[$player->getName()]["j"])) {
	$this->players[$player->getName()]["j"]=0;
	$this->players[$player->getName()]["x3"]=$player->getFloorX();
	$this->players[$player->getName()]["y3"]=$player->getFloorY();
	$this->players[$player->getName()]["z3"]=$player->getFloorZ();
	$this->players[$player->getName()]["x"]=$player->getX();
	$this->players[$player->getName()]["y"]=$player->getY();
	$this->players[$player->getName()]["z"]=$player->getZ();
	$this->players[$player->getName()]["te"]=false;
    $this->players[$player->getName()]["w"]=0;
}
	$this->players[$player->getName()]["c"]=false;
if(!isset($this->players[$player->getName()]["t"])) {
$this->players[$player->getName()]["t"]=0;
}
            $block = $event->getPlayer()->getLevel()->getBlock(new Vector3($player->getX(),$player->getY()-1,$player->getZ()));
			$block0 = $event->getPlayer()->getLevel()->getBlock(new Vector3($player->getX(),$player->getY(),$player->getZ()));
			if($player->isOnGround() or !($block->getID() == 0 and !$block->getID() == 10 and !$block->getID() == 11 and !$block->getID() == 8 and !$block->getID() == 9 and !$block->getID() == 182 and !$block->getID() == 126 and !$block->getID() == 44 and $block0->getID() == 0)) { $this->players[$player->getName()]["t"]=0;
			} else ++$this->players[$player->getName()]["t"];
			//$this->getLogger()->warning($player->getY()-$this->players[$player->getName()]["y"]);
           
		if($this->players[$player->getName()]["t"]>30) {
                   
                   $this->getLogger()->warning($player->getName()."自动清楚Hack Type:Fly");
                     $event->getPlayer()->sendMessage("§c§l[FWatchDog]".TextFormat::RED."§b自动检测§e:Do not use fly §bType:Fly");
                }
                if ($this->players[$player->getName()]["t"]>=15) {
			$floorv3 = $this->findfloor($player->getLevel(), new Vector3($player->getFloorX(),$player->getFloorY(),$player->getFloorZ()));
						if ($floorv3 === false) {  //脚下是虚空
						}
						else {
         						//$player->setMotion(new Vector3(0,50,0));
								$player->teleport($floorv3);
								$this->players[$player->getName()]["c"]=true;
						          $event->getPlayer()->sendMessage("§c§l[FWatchDog]".TextFormat::RED."§b自动检测§e:Do not fly §bType:Fly");
		                        //$this->players[$player->getName()]["t"]=0;
						}
			}
			        if($this->players[$player->getName()]["t"]>6){ 
			        $player->setMotion(new Vector3(0,-1.5,0));
			        						$this->getLogger()->warning($player->getName()."触发安全警报");
			        					   $event->getPlayer()->sendMessage("§c§l[FWatchDog]".TextFormat::RED."§b自动检测§e:Do not fly §bType:Fly");
			        }

					$p=$player->getName();
$pcod=new Vector3($player->getX(),0,$player->getZ());
If(isset($this->move[$p]) and isset($this->time[$p])){
if($pcod !== $this->move[$p] and $this->getMillisecond() !== $this->time[$p] and !$this->players[$player->getName()]["te"] and $player->getLevel()->getFolderName()==$this->players[$player->getName()]["l"]){
$speed = $pcod->distance($this->move[$p])/($this->getMillisecond() - $this->time[$p]);
$spy=($player->getY()-$this->players[$player->getName()]["y"])/($this->getMillisecond() - $this->time[$p]);
//$this->getLogger()->warning($spy);
     if(($block->getID()>=8 and $block->getID()<=11) and !($block0->getID()>=8 and $block0->getID()<=11) and ($spy>0) and !$player->isInsideOfSolid()) { 
                   //$player->setMotion(new Vector3(0,-0.01,0));
                   ++$this->players[$player->getName()]["w"];
                } else $this->players[$player->getName()]["w"]=0;
                if($this->players[$player->getName()]["w"]>=5) {
                  $this->getLogger()->warning($player->getName()."自动清楚Hack Type:Light work ".$this->players[$player->getName()]["w"]);
                  
                    $event->getPlayer()->sendMessage("§c§l[FWatchDog]".TextFormat::RED."§b自动检测§e:You are on the water,so we think you use the Float  §bType:Float");
                  //$player->setPosition(new Vector3($player->getX(),$player->getY()-$this->players[$player->getName()]["w"]*2,$player->getZ()));
                }


//$this->getLogger()->notice($p." ".$speed);
		if($speed>=0.007) {
				$this->players[$player->getName()]["j"]++;
			} else 
			{
				$this->players[$player->getName()]["j"]=0;
			}
if($speed <= 0.004) {
				$this->players[$player->getName()]["x3"]=$player->getFloorX();
				$this->players[$player->getName()]["y3"]=$player->getFloorY();
				$this->players[$player->getName()]["z3"]=$player->getFloorZ();
}
if($speed >= 0.1) {
$event->setCancelled();
$this->getLogger()->warning($player->getName()."瞬移".$speed);
  $event->getPlayer()->sendMessage("§c§l[FWatchDog]".TextFormat::RED."§b自动检测§e:The action is suspicious  §bType:Transmission".$speed);
}
 if($this->players[$player->getName()]["j"]>=5){
$player->teleport($this->findfloor($player->getLevel(),new Vector3($this->players[$player->getName()]["x3"],$this->players[$player->getName()]["y3"],$this->players[$player->getName()]["z3"])));
$this->getLogger()->warning($player->getName()."疾跑".$this->players[$player->getName()]["j"]);
  $event->getPlayer()->sendMessage("§c§l[FWatchDog]".TextFormat::RED."§b自动检测§e:Too fast §bType:Speed §d§l速度:".$speed);
}
}
}
$this->move[$p] = $pcod;
$this->time[$p] = $this->getMillisecond();

			        $this->players[$player->getName()]["x"]=$player->getX();
			        $this->players[$player->getName()]["y"]=$player->getY();
			        $this->players[$player->getName()]["z"]=$player->getZ();
           $this->players[$player->getName()]["l"]=$player->getLevel()->getFolderName();
           $this->players[$player->getName()]["te"]=false;
			}
}

	public function onTeleport(EntityTeleportEvent $event) {
		$player = $event->getEntity();
		if($player instanceof Player) {
			//防止传送时被拉回来
			if(isset($this->players[$player->getName()]) && isset($this->players[$player->getName()]["c"])) 
				if(!$this->players[$player->getName()]["c"]) $this->players[$player->getName()]["te"] = true;
		}
	}
    public function getMillisecond() {
		list($s1, $s2) = explode(' ', microtime());		
		return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);	
	}
	public function findfloor($level, $v3) {
		$y = $v3->getY();
		do {
   			$y = $y - 1;
			$v3->y = $y;
			$block = $level->getBlock($v3);
			$id = $block->getID();
			//echo ($id." ");
		}
		while ($id == 0 and $y >= 0);
		
		if ($y < 0) {  //脚下是虚空
			return false;
		}
		else {
			$v3->y = $v3->y + 1;
			return $v3;
		}
	}
	public function onJoin(PlayerJoinEvent $event) {
		$this->players[$event->getPlayer()->getName()]["c"]=false;
		
		
		
		
	}
	
	public function onPlayerChat(PlayerChatEvent $event)
	{
		$name=$event->getPlayer()->getName();
		$nowday=date("d");
		$nowhour=date("H");
		$nowminute=date("i");
		$nowsec=date("s");
        if(isset($this->messaget[$name])){
	       if(!$nowhour=$this->messaget[$name]["hour"] or !$nowminute=$this->messaget[$name]["minute"]){
			   $this->messaget[$name]["minute"] = $nowminute;
			$this->messaget[$name]["day"] = $nowday;
			$this->messaget[$name]["hour"] = $nowhour;
			$this->messaget[$name]["sec"] = $nowsec;
		   }else{
			   if($nowsec < ($this->messaget[$name]["sec"] + $this->settime)){
				   $event->setCancelled(true);
				   $event->getPlayer()->sendMessage("§c§l[FWatchDog]".TextFormat::RED."§b自动检测§e:Do not spam §bType:SPAM");
			   }else{
				   			$this->messaget[$name]["sec"] = $nowsec;
			   }
		   }
		}else{
			$this->messaget[$name]["minute"] = $nowminute;
			$this->messaget[$name]["day"] = $nowday;
			$this->messaget[$name]["hour"] = $nowhour;
			$this->messaget[$name]["sec"] = $nowsec;
		}
	}
	
	
	
//Crit
public function onHurt(EntityDamageEvent $event){
	if($event instanceof EntityDamageByEntityEvent) {
		if($event->getDamager() instanceof Player) {
			$pl = $event->getDamager();
			$air = $pl->getLevel()->getBlock(new Vector3($pl->x, $pl->y - 0.75, $pl->z))->getId();
			$air2 = $pl->getLevel()->getBlock(new Vector3($pl->x, $pl->y, $pl->z))->getId();
			if($air === 0 and $air2 === 0 and !$pl->hasEffect(Effect::BLINDNESS)) {
				$et = $event->getEntity();
				$ev = new CriticalEvent($pl, $et);
				$this->getServer()->getPluginManager()->callEvent($ev);
				if(!$ev->isCancelled()) {
				$pl->sendPopup(C::RED."§c§l[FWatchDog]§b自动检测§e:Do not use killing halo §bType:KilingHalo");
				$event->setDamage($event->getDamage(EntityDamageByEntityEvent::MODIFIER_BASE) * 1.5);
				$particle = new CriticalParticle(new Vector3($et->x, $et->y + 1, $et->z));
					$random = new Random((int) (microtime(true) * 1000) + mt_rand());
					for($i = 0; $i < 60; ++$i){
						$particle->setComponents(
						$et->x + $random->nextSignedFloat() * $et->x,
						$et->y + 1.5 + $random->nextSignedFloat() * $et->y + 1.5,
						$et->z + $random->nextSignedFloat() * $et->z
						);
			      $pl->getLevel()->addParticle($particle);
				  }
				}
			}
		}
	}
}	

//No Brush object 
public function onScc(PlayerDropItemEvent $e){
$p=$e->getPlayer();
if($e->getPlayer()->getGamemode(1)){
$e->setCancelled(true);
$p->sendMessage("§c§l[FWatchDog]§b自动判断§eYou can not Throw Articles in Create §cType:Hack");}
   }













	//Snowball Strike 

	public function onProjectileLaunch(ProjectileLaunchEvent $event){
		$entity = $event->getEntity();
		if($entity instanceof Snowball){
			$shooter = $entity->shootingEntity;
			if($shooter instanceof Player){
				$id = $shooter->getId();
//				$this->getLogger()->info($id);
				if( array_key_exists($id,$this->order) ){$this->order[$id]++;}
					else{$this->order += array($id => 1);}
			}
		}
	}
	
	public function onPlayerDeath(PlayerDeathEvent $event){
		$entity = $event->getEntity();
		if($entity instanceof Player){
			$id = $entity->getId();
//			$this->getLogger()->info($entity->getName()."is dead");
			if(array_key_exists($id,$this->order)){$this->order[$id]=0;}
		}
	}
	
	public function onEntityClose(EntityDespawnEvent $event){
		if($event->getType() === 81){	//81=Snowball
			$entity = $event->getEntity();
			$shooter = $entity->shootingEntity;
			$posTo = $entity->getPosition();
			
			if($shooter instanceof Player && $posTo instanceof Position){
				$id = $shooter->getId();
				if(array_key_exists($id,$this->order) && $this->order[$id]>0){
					$this->order[$id]--;
					$posFrom = $shooter->getPosition();
//					$this->getLogger()->info($shooter->getName()." is at ".$posTo->__toString() );
					$shooter->teleport($posTo);
					$shooter->attack(5);
				}
			}
		}
	}
	//Kernel NPL
		
	public function Kernel()
	{
		
			self::$instance = $this;
			$this->server = $this->getServer();
			
				$this->getServer()->getLogger()->info(self::NORMAL_PRE."§b读取成功".self::NORMAL_PRE, "info", "server");
				$this->getServer()->getLogger()->info(self::NORMAL_PRE."§b兼容核心§c{$this->server->getName()}", "info", "server");
	      		
			
		}
		
	}
	
	
	
	
	