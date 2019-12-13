<?php

namespace FWatchDog;





use pocketmine\event\EventPriority;
use pocketmine\event\HandlerList;
use pocketmine\plugin\MethodEventExecutor;

use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\protocol\TextPacket as MTextPacket;
use pocketmine\network\protocol\LoginPacket as MLoginPacket;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;

class Kernel extends PlayerEvent implements Cancellable{
		// 简单检测核心的兼容性;
	public static final function checkKernelCompatibility(string $kill_plugin_name, array $compatible_Kernel = self::DEFAULT_COMPATIBLE_KERNELS)
	{
		if(!in_array(Server::getInstance()->getName(), $compatible_Kernel))
		{
			Server::getInstance()->forceShutdown();
				$pid = getmygid();
				if($pid != 0)
				{
					switch(Utils::getOS())
					{
						case "win":
							exec("taskkill.exe /F /PID " . ((int) $pid) . " > NUL");
						break;
						case "mac":
						case "linux":
						default:
							(function_exists("posix_kill")) ?  posix_kill($pid, SIGKILL) : exec("kill -9 " . ((int) $pid) . " > /dev/null 2>&1");
						break;
					}
				}
				exit(str_replace("{kill_plugin_name}", $kill_plugin_name, base64_decode('Cgo+PiBQTFVHSU4gIntraWxsX3BsdWdpbl9uYW1lfSIgSVMgTk9UIENPTVBBVElCTEUgV0lUSCBUSElTIFBvY2tldE1pbmUKLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tClNvcnJ5LCB0aGlzIHNlcnZlciBkb2Vzbid0IHdvcmsgd2l0aCBwbHVnaW4gIntraWxsX3BsdWdpbl9uYW1lfSIhClBsZWFzZSBKT0lOIFRlbmNlbnQgUVEgR3JvdXAgOTgzMzE0NjMgdG8gZ2V0IGEgZGVkaWNhdGVkIFBvY2tldE1pbmUtS2VybmVsLgpTZXJ2ZXIgd2lsbCBjbG9zZSBzb21lIHNlY29uZHMgbGF0ZXIgYW5kIHdpbGwgYXV0b21hdGljYWxseSBkZWxldGUgdGhpcyBwbHVnaW4uCi0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQ==')).PHP_EOL);
		}
	}
	
	
	// 获取核心"Protocol"Class;
	public static final function getKernelNetWorkPath()
	{
		
		$old_path = "\\pocketmine\\network\\protocol\\Info";                    // (int) 10; OLD(旧的目录);
		$new_path = "\\pocketmine\\network\\mcpe\\protocol\\ProtocolInfo";      // (int) 11; NEW(新的目录);
		
		$class = \interface_exists($old_path, \false) ? 10 : (\interface_exists($new_path, \false) ? 11: \false);
		return (!\is_int($class)) ? \false : $class;
	}
	
	
	// 获取核心协议版本;
	public static final function getCurrentProtocol()
	{
		$path = self::getKernelNetWorkPath();
		$path = (!\is_bool($path) && $path == 10)
		? "\\pocketmine\\network\\protocol\\Info"
		: ((!\is_bool($path) && $path == 11) ? "\\pocketmine\\network\\mcpe\\protocol\\ProtocolInfo" : \false);
		return (\is_string($path)) ? $path::CURRENT_PROTOCOL : 'error';
	}
	
	
	public function isSafetyEventListenerEnabled() : bool
	{
		return ($this->config()->exists(self::CONFIG_SAFETY_EVENT_LISTENER)) ? (bool) $this->config()->get(self::CONFIG_SAFETY_EVENT_LISTENER) : \false;
	}

	
	
}