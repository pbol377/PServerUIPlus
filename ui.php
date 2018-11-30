<?php
namespace PSPUI;

use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket; 
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\Player;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;


class ui extends PluginBase implements Listener {

public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents ($this, $this);
		@mkdir ( $this->getDataFolder () );
      //오프: 0 온: 1
      $this->onoff = new Config($this->getDataFolder() . "onoff.yml", Config::YAML,[
      "#주의: 명령어 갯수는 CBan.yml의 인자 갯수랑 같아야합니다.",
      "채팅" => "0",
      "공격" => "0",
      "블럭" => "0",
      "움직임" => "0",
      "명령어" => "0",
      "명령어갯수" => 0
         ]);
         $this->data = $this->onoff->getAll();
         $this->cban = new Config($this->getDataFolder() . "CBan.yml", Config::YAML);
         $this->cb = $this->cban->getAll();
         $this->cb[0] = "제어";
         $this->save();
}

public function onMove(PlayerMoveEvent $event) {
	$player = $event->getPlayer();
    if (! $player->isOp ()) {
		if($this->data["움직임"] == "1"){
			$player->sendMessage("§l§c현재 움직임이 제한되어 있습니다");
              $event->setCancelled();
           }
		}
	}
	
public function onPCP(PlayerCommandPreprocessEvent $event) {
	$player = $event->getPlayer();
	$cmd = $event->getMessage();
    if (! $player->isOp ()) {
		if($this->data["명령어"] == "1"){
			$player->sendMessage("§l§c현재 명령어 사용이 제한되어 있습니다");
              $event->setCancelled();
           }
        for($i=0;$i<$this->data["명령어갯수"];$i++){
        	if($cmd == "/".$this->cb[$i]){
               $player->sendMessage("§l§c해당 명령어는 사용이 제한되어 있습니다");
              $event->setCancelled();
              break;
           }
        }
		}
	}
	
public function place(BlockPlaceEvent $event){
	$player = $event->getPlayer();
    if (! $player->isOp ()) {
		if($this->data["블럭"] == "1"){
			$player->sendMessage("§l§c현재 블럭 설치가 제한되어 있습니다");
              $event->setCancelled();
           }
		}
	}
	
	public function onChat(PlayerChatEvent $event) {
	$player = $event->getPlayer();
    if (! $player->isOp ()) {
		if($this->data["채팅"] == "1"){
			$player->sendMessage("§l§c현재 채팅이 제한되어 있습니다");
              $event->setCancelled();
           }
		}
	}
	
	public function EntityDamageEvent_Player(EntityDamageEvent $event){
		   if($event instanceof EntityDamageByEntityEvent){
            if($event->getEntity() instanceof Player and $event->getDamager() instanceof Player){
            	$player = $event->getDamager();
                if (! $player->isOp ()) {
		            if($this->data["채팅"] == "1"){
			           $player->sendMessage("§l§c현재 공격이 제한되어 있습니다");
                       $event->setCancelled();
                    }
		          }
	           }
            }
           }
        

public function sendUI(Player $p, $c, $d) {
		$pack = new ModalFormRequestPacket();
		$pack->formId = $c;
		$pack->formData = $d;
		$p->dataPacket($pack);
	}
	
	public function OpenUiF() {
		$a = $this->data["채팅"];
	if($a == "1") {
		$a = "§l§c제한중";
		}
	else{
		$a = "§l§a풀림";
		}
		
		$b = $this->data["공격"];
	if($b == "1") {
		$b = "§l§c제한중";
		}
	else{
		$b = "§l§a풀림";
		}
		
		$c = $this->data["블럭"];
	if($c == "1") {
		$c = "§l§c제한중";
		}
	else{
		$c = "§l§a풀림";
		}
		
		$d = $this->data["움직임"];
	if($d == "1") {
		$d = "§l§c제한중";
		}
	else{
		$d = "§l§a풀림";
		}
	$e = $this->data["명령어"];
	if($e == "1") {
		$e = "§l§c제한중";
		}
	else{
		$e = "§l§a풀림";
		}
         $encode = [
		"type" => "form",
		"title" => "§l§c서버 제어판",
		"content" => "§l<< 서버 제어판입니다 >>\n\nVersion 1.4\n\n채팅 제한: ".$a."\n\n§f공격 제한: ".$b."\n\n§f블럭 파괴 제한: ".$c."\n\n§f움직임 제한: ".$d."\n\n§f명령어 제한: ".$e,
		"buttons" => [
		[
		"text" => "§l메뉴 열기",
		],
		[
		"text" => "§l전체 공지하기",
		],
		[
		"text" => "§l명령어 밴 메뉴 열기",
		],
		[
		"text" => "§l나가기",
		]
		]
		];
		return json_encode($encode);
	}
	
public function OpenUiCB() {
	
$encode = [
		"type" => "form",
		"title" => "§l§c명령어 제어판",
		"content" => "§l명령어 메뉴입니다\n\n",
		"buttons" => [
		[
		"text" => "§l명령어 밴 목록 열기",
		],
		[
		"text" => "§l명령어 밴 하기",
		],
		[
		"text" => "§l명령어 밴 제거하기",
		],
		[
		"text" => "§l메인",
		],
		[
		"text" => "§l나가기",
		]
		]
		];
		return json_encode($encode);
	}

public function OpenUi() {
		
         $encode = [
		"type" => "form",
		"title" => "§l§c메뉴",
		"content" => "§l서버 메뉴",
		"buttons" => [
		[
		"text" => "§l채팅 제한",
		],
		[
		"text" => "§l공격 제한",
		],
		[
		"text" => "§l블럭 부숨 제한",
		],
		[
		"text" => "§l움직임 제한",
		],
		[
		"text" => "§l명령어 제한",
		],
		[
		"text" => "§l메인",
		],
		[
		"text" => "§l나가기",
		]
		]
		];
		return json_encode($encode);
	}

public function OpenUi2() {
	$a = $this->data["채팅"];
	if($a == "1") {
		$a = "§l§c제한중";
		}
	else{
		$a = "§l§a풀림";
		}
		$encode = [
		"type" => "form",
		"title" => "§l§a채팅 제한 패널",
		"content" => "§l제어 상태: ".$a ,
		"buttons" => [
		[
		"text" => "§l§c제한",
		],
		[
		"text" => "§l§a풀기",
		],
		[
		"text" => "§l나가기",
		]
		]
		];
		return json_encode($encode);
	}
	
	public function OpenUi3() {
	$a = $this->data["공격"];
	if($a == "1"){
		$a = "§c§l제한중";
		}
	else{
		$a = "§a§l풀림";
		}
		$encode = [
		"type" => "form",
		"title" => "§l§a공격 제한 패널",
		"content" => "§l제어 상태: ".$a ,
		"buttons" => [
		[
		"text" => "§l§c제한",
		],
		[
		"text" => "§l§a풀기",
		],
		[
		"text" => "§l나가기",
		]
		]
		];
		return json_encode($encode);
	}
	
	public function OpenUi4() {
	$a = $this->data["블럭"];
	if($a == "1"){
		$a = "§l§c제한중";
		}
	else{
		$a = "§a§l풀림";
		}
		$encode = [
		"type" => "form",
		"title" => "§l§a블럭 부숨 제한 패널",
		"content" => "§l제어 상태: ".$a ,
		"buttons" => [
		[
		"text" => "§l§c제한",
		],
		[
		"text" => "§l§a풀기",
		],
		[
		"text" => "§l나가기",
		]
		]
		];
		return json_encode($encode);
	}
	
	public function OpenUi5() {
	$a = $this->data["움직임"];
	if($a == "1"){
		$a = "§c§l제한중";
		}
	else{
		$a = "§a§l풀림";
		}
		$encode = [
		"type" => "form",
		"title" => "§l§a움직임 제한 패널",
		"content" => "§l제어 상태: ".$a ,
		"buttons" => [
		[
		"text" => "§l§c제한",
		],
		[
		"text" => "§l§a풀기",
		],
		[
		"text" => "§l나가기",
		]
		]
		];
		return json_encode($encode);
	}

public function OpenUi6() {
	
		$encode = [
		"type" => "custom_form",
		"title" => "§l전체 공지 발송",
		"content" => [
		[
		"type" => "input",
		"text" => "§l전체 공지를 입력해주세요\n",
		]
		]
		];
		return json_encode($encode);
	}
	
public function OpenUi100() {
	
		$encode = [
		"type" => "custom_form",
		"title" => "§l§c명령어 밴 항목 추가",
		"content" => [
		[
		"type" => "input",
		"text" => "§l§c※밴할 명령어를 입력해주세요.\n(슬래시 '/'는 포함되서는 안됩니다)\n",
		]
		]
		];
		return json_encode($encode);
	}
	
public function OpenUi1000() {
	
		$encode = [
		"type" => "custom_form",
		"title" => "§l§c명령어 밴 항목 추가",
		"content" => [
		[
		"type" => "input",
		"text" => "§l§c※밴할 명령어를 입력해주세요.\n(슬래시 '/'는 포함되서는 안됩니다)\n\n*필수 입력 사항입니다\n",
		]
		]
		];
		return json_encode($encode);
	}
	
public function OpenUi101() {
	
		$encode = [
		"type" => "custom_form",
		"title" => "§l§c명령어 밴 항목 제거",
		"content" => [
		[
		"type" => "input",
		"text" => "§l§c※제거할 밴 된 명령어를 입력해주세요.\n(슬래시 '/'는 포함해서는 안됩니다)\n",
		]
		]
		];
		return json_encode($encode);
	}
	
public function OpenUi1010() {
	
		$encode = [
		"type" => "custom_form",
		"title" => "§l§c명령어 밴 항목 제거",
		"content" => [
		[
		"type" => "input",
		"text" => "§l§c※제거할 밴 된 명령어를 입력해주세요.\n(슬래시 '/'는 포함해서는 안됩니다)\n\n*필수 입력 사항입니다\n",
		]
		]
		];
		return json_encode($encode);
	}
	
public function OpenUi102($a) {
	
		$encode = [
		"type" => "form",
		"title" => "§l§c명령어 밴 추가",
		"content" => "§l명령어 밴 항목에 추가 되었습니다.\n\n§f추가 명령어:\n \n ".$a ,
		"buttons" => [
		[
		"text" => "§l메인",
		],
		[
		"text" => "§l나가기",
		]
		]
		];
		return json_encode($encode);
	}
	
public function OpenUi103($a) {
	
		$encode = [
		"type" => "form",
		"title" => "§l§c명령어 밴 제거",
		"content" => "§l명령어 밴 항목에서 제거 되었습니다.\n\n§f제거 명령어: \n\n ".$a ,
		"buttons" => [
		[
		"text" => "§l메인",
		],
		[
		"text" => "§l나가기",
		]
		]
		];
		return json_encode($encode);
	}

public function OpenUi8( $a ) {
	
		$encode = [
		"type" => "form",
		"title" => "§l§a전체공지",
		"content" => "§l전체 공지가 발송되었습니다.\n\n§f내용: \n ".$a ,
		"buttons" => [
		[
		"text" => "§l메인",
		],
		[
		"text" => "§l나가기",
		]
		]
		];
		return json_encode($encode);
	}

public function OpenUi9() {
$a = $this->data["명령어"];
	if($a == "1"){
		$a = "§c§l제한중";
		}
	else{
		$a = "§a§l풀림";
		}
		$encode = [
		"type" => "form",
		"title" => "§l§a명령어 제한 패널",
		"content" => "§l제어 상태: ".$a ,
		"buttons" => [
		[
		"text" => "§l§c제한",
		],
		[
		"text" => "§l§a풀기",
		],
		[
		"text" => "§l나가기",
		]
		]
		];
		return json_encode($encode);
	}

public function onDataPacketRecieve(DataPacketReceiveEvent $event) {
		$packet = $event->getPacket();
		$player = $event->getPlayer();
		if ($packet instanceof ModalFormResponsePacket) {
			$id = $packet->formId;
			$a = json_decode($packet->formData, true);
			
			if ($id === 12345) {
			if ($a === 0) {//메뉴
					$this->sendUI($player, 22345, $this->OpenUi());
					return;
				} 
			
			else if ($a === 1) {//공지
				$this->sendUI($player, 1115, $this->OpenUi6());
				return;
				}
			
			else if($a === 2){//명령어 메뉴
				$this->sendUI($player, 55555, $this->OpenUiCB());
				return;
				}
			
			else if($a === 3){//나가기
				$player->sendMessage("§l정상적으로 나갔습니다");
				return;
				}
			}
			
			if ($id === 55555) {
				
			if ($a === 0) {//밴 목록
			  $player->sendMessage("§l§c밴목록: \n");
					for($i=0;$i<$this->data["명령어갯수"];$i++){
						if($this->cb[$i] != "제거된 명령어입니다"){
                           $player->sendMessage("/".$this->cb[$i]."\n");
                           }
                    }
					return;
				} 
			
			else if ($a === 1) {//밴 추가
				$this->sendUI($player, 9998, $this->OpenUi100());
				return;
				}
			
			else if($a === 2){//밴 제거
				$this->sendUI($player, 9999, $this->OpenUi101());
				return;
				}
			
			else if($a === 3){//메인
				$this->sendUI($player, 12345, $this->OpenUiF());
				return;
				}
			
			else if($a === 4){//나가기
				$player->sendMessage("§l정상적으로 나갔습니다");
				return;
				}
			}
			
			else if ($id === 9998) {//밴추가
            	if (!isset ($a[0])) {
					$this->sendUI($player, 12345, $this->OpenUi7());
					return;
					}
				else {
					if($a[0] != ""){
					$this->cb[$this->data["명령어갯수"]] = $a[0];
					$this->data["명령어갯수"]++;
					$this->save();
					$this->sendUI($player, 9997, $this->OpenUi102($a[0]));
					return;
					}
					else{
						$this->sendUI($player, 999900, $this->OpenUi1000());
						return;
						}
					return;
					}
				}
			
			else if ($id === 999900) {//밴추가 오류
            	if (!isset ($a[0])) {
					$this->sendUI($player, 12345, $this->OpenUi7());
					return;
					}
				else {
					if($a[0] != ""){
					$this->cb[$this->data["명령어갯수"]] = $a[0];
					$this->data["명령어갯수"]++;
					$this->save();
					$this->sendUI($player, 9997, $this->OpenUi102($a[0]));
					return;
					}
					else{
						$this->sendUI($player, 999900, $this->OpenUi1000());
						return;
						}
					return;
					}
				}
			
			else if ($id === 9999) {//밴제거
            	if (!isset ($a[0])) {
					$this->sendUI($player, 12345, $this->OpenUi7());
					return;
					}
				else {
					if($a[0] != ""){
					for($i=0;$i<$this->data["명령어갯수"];$i++){
        	           if($this->cb[$i] == "$a[0]"){
                           $this->cb[$i] = "제거된 명령어입니다";
                           $this->save();
                           $this->sendUI($player, 9996, $this->OpenUi103($a[0]));
                           break;
                         }
                     }
					return;
					}
					else {
                    $this->sendUI($player, 999901, $this->OpenUi1010());
                      }
					}
				}
				
			else if ($id === 999901) {//밴제거 오류
            	if (!isset ($a[0])) {
					$this->sendUI($player, 12345, $this->OpenUi7());
					return;
					}
				else {
					if($a[0] != ""){
					for($i=0;$i<$this->data["명령어갯수"];$i++){
        	           if($this->cb[$i] == "$a[0]"){
                           $this->cb[$i] = "제거된 명령어입니다";
                           $this->save();
                           return;
                         }
                     }
					$this->sendUI($player, 9996, $this->OpenUi103($a[0]));
					return;
					}
					else {
                    $this->sendUI($player, 999901, $this->OpenUi1010());
                    return;
                      }
					}
				}
			
			else if ($id === 9997){//밴 확인
            	if($a === 0)
            {
            	$this->sendUI($player, 12345, $this->OpenUiF());
            	return;
            }
            else if($a === 1){
           $player->sendMessage("§l정상적으로 나갔습니다");
            return;
              }
          }    
            else if ($id === 9996){//밴 제거 확인
            	if($a === 0)
            {
            	$this->sendUI($player, 12345, $this->OpenUiF());
            	return;
            }
            else if($a === 1){
           $player->sendMessage("§l정상적으로 나갔습니다");
            return;
              }
			}
             else if ($id === 22345) {//메뉴
				
                if ($a === 0) {//채팅
					$this->sendUI($player, 54321, $this->OpenUi2());
					return;
				} 
                
                else if ($a === 1) {//공격
					$this->sendUI($player, 1112, $this->OpenUi3());
					return;
				}
				
				else if ($a === 2) {//블럭
					$this->sendUI($player, 1113, $this->OpenUi4());
					return;
				}
				
				else if ($a === 3) {//움직임
					$this->sendUI($player, 1114, $this->OpenUi5());
					return;
				}
				else if ($a === 4) {//명령어
					$this->sendUI($player, 1120, $this->OpenUi9());
					return;
				}
				else if ($a === 5) {//돌아가기
					$this->sendUI($player, 12345, $this->OpenUiF());
					return;
				}
				else if ($a === 6) {//나가기
					$player->sendMessage("§l정상적으로 나갔습니다");
					return;
				}
              }
              
            else if ($id === 54321){
            	if($a === 2)
            {
            	$player->sendMessage("§l정상적으로 나갔습니다");
            	return;
            }
            else if($a === 0){
         $this->data["채팅"] = "1";
            $this->save();
            $player->sendMessage("§c§l채팅이 제한 되었습니다");
            $this->sendUI($player, 12345, $this->OpenUiF());
            return;
             }
            else if($a === 1){
            $this->data["채팅"] = "0";
            $this->save();
            $player->sendMessage("§a§l채팅이 허용 되었습니다");
            $this->sendUI($player, 12345, $this->OpenUiF());
            return;
                 }
              }
              
              else if ($id === 1112){
            	if($a === 2)
            {
            	$player->sendMessage("§l정상적으로 나갔습니다");
            	return;
            }
            else if($a === 0){
           $this->data["공격"] = "1";
            $this->save();
            $player->sendMessage("§c§l공격이 제한 되었습니다");
            $this->sendUI($player, 12345, $this->OpenUiF());
            return;
              }
            else if($a === 1){
            $this->data["공격"] = "0";
            $this->save();
            $player->sendMessage("§a§l공격이 허용 되었습니다");
            $this->sendUI($player, 12345, $this->OpenUiF());
            return;
                 }
              }
              
              else if ($id === 1120){
            	if($a === 2)
            {
            	$player->sendMessage("§l정상적으로 나갔습니다");
            	return;
            }
            else if($a === 0){
           $this->data["명령어"] = "1";
            $this->save();
            $player->sendMessage("§c§l명령어 사용이 제한 되었습니다");
            $this->sendUI($player, 12345, $this->OpenUiF());
            return;
              }
            else if($a === 1){
            $this->data["명령어"] = "0";
            $this->save();
            $player->sendMessage("§a§l명령어 사용이 허용 되었습니다");
            $this->sendUI($player, 12345, $this->OpenUiF());
            return;
                 }
              }
              
              else if ($id === 1113){
            	if($a === 2)
            {
            	$player->sendMessage("§l정상적으로 나갔습니다");
            	return;
            }
            else if($a === 0){
            $this->data["블럭"] = "1";
            $this->save();
            $player->sendMessage("§c§l블럭 파괴가 제한 되었습니다");
            $this->sendUI($player, 12345, $this->OpenUiF());
            return;
            }
            else if($a === 1){
            $this->data["블럭"] = "0";
            $this->save();
            $player->sendMessage("§a§l블럭 파괴가 허용 되었습니다");
            $this->sendUI($player, 12345, $this->OpenUiF());
            return;
            }
              }
              
              else if ($id === 1114){
            	if($a === 2)
            {
            	$player->sendMessage("§l정상적으로 나갔습니다");
            	return;
            }
            else if($a === 0){
            $this->data["움직임"] = "1";
            $this->save();
            $player->sendMessage("§c§l이동이 제한 되었습니다");
            $this->sendUI($player, 12345, $this->OpenUiF());
            return;
            }
            else if($a === 1){
            $this->data["움직임"] = "0";
            $this->save();
            $player->sendMessage("§a§l이동이 허용 되었습니다");
            $this->sendUI($player, 12345, $this->OpenUiF());
            return;
            }
              }
              
            else if ($id === 1115) {//공지하기
            	if (!isset ($a[0])) {
					$this->sendUI($player, 1117, $this->OpenUi7());
					return;
					}
				else {
					$this->getServer()->broadcastMessage("§l§a[ §f전체 공지 §a]§f ".$a[0]);
					$this->sendUI($player, 1117, $this->OpenUi8($a[0]));
					return;
					}
				}
				
			else if ($id === 1116) {//명령어 밴
 
				if(isset ($a[0])){
					$this->getServer()->broadcastMessage("§l§a[ §c명령어밴 §a]§f ".$a[0]);
					$this->cb[$this->data["명령어갯수"]] = $a[0];
					$this->data["명령어갯수"] = $this->data["명령어갯수"] + 1;
					$this->save();
					$this->sendUI($player, 12345, $this->OpenUiF());
					return;
					}
				}
				
				else if ($id === 1117){//전체공지 확인
            	if($a === 0)
            {
            	$this->sendUI($player, 12345, $this->OpenUiF());
            	return;
            }
            else if($a === 1){
           $player->sendMessage("§l정상적으로 나갔습니다");
            return;
              }
              }
            }
         }
         
         
         
public function onCommand(Commandsender $sender, Command $command, string $label, array $args) : bool{
		if ($command->getName() === "제어") {
			if(!$sender instanceof Player) {
        $sender->sendMessage ("§c§l콘솔에서는 실행이 불가능합니다." );
        return true;
        }
        if (! $sender->isOp ()) {
        	$sender->sendMessage ("§c이 명령어를 사용할 권한이 없습니다." );
       return true;
        }
			$this->sendUI($sender, 12345, $this->OpenUiF());
		}
		return true;
	}
	
public function save(){
		$this->onoff->setAll($this->data);
		$this->onoff->save();
		$this->cban->setAll($this->cb);
		$this->cban->save();
	}
}
