<?php

class Main extends TelegramApp\Module {
	public function run(){
		$this->user->load();
		parent::run();
	}

	protected function hooks(){
		if(
			strpos($this->user->step, "REGISTER_") === 0 and
			$this->telegram->text() and
			!$this->telegram->text_command() and
			!$this->telegram->text_hashtag() and
			!$this->telegram->text_url()
		){
			return $this->register($this->user->step);
		}
	}

	private function register($step){
		if($step == "REGISTER_NAME"){
			$text = trim($this->telegram->text(TRUE));
			$error = NULL;

			if($this->telegram->words() > 1 or strlen($text) > 24){
				$error = "Nah. Demasiado largo. Prueba con otro.";
			}elseif($this->telegram->text_has_emoji()){
				$error = "Será todo lo cuqui que quieras. Pero sin emojis.";
			}elseif(empty($text)){
				$error = "No alcanzo a ver tu nombre.";
			}

			if($error){
				$this->telegram->send
					->text($error)
				->send();

				$this->end();
			}

			$this->user->name = $text;
			$this->user->step = "REGISTER_CLASS";

			$this->telegram->send
				->text("Guay! A partir de ahora, todo el mundo te conocerá como el famoso <b>$text</b>.", "HTML")
			->send();

			$this->end();
		}
	}

	public function start($data = NULL){
		if($this->telegram->is_chat_group()){
			$this->telegram->send
				->inline_keyboard()
					->row_button("Iniciar", "https://t.me/BossBattle_bot")
				->show()
				->text("Ábreme por privado.")
			->send();

			$this->end();
		}

		// Comprobar el usuario.
		if(!isset($this->user->name)){
			$ref = NULL;
			if(is_numeric($data)){
				$ref = $data;
			}
			// TODO comprobar referal a través de código start.
			return $this->first_time($ref);
		}
	}

	private function first_time($ref = NULL){
		if($this->telegram->is_chat_group()){ $this->end(); }

		$str = "Si has llegado hasta aquí, significa que vienes buscando guerra... Y guerra tendrás.\n"
				."¿Estás preparado?";

		$this->telegram->send
			->caption($str)
		->file("photo", "https://www.walldevil.com/wallpapers/a80/battle-field-war-soldier-warrior-sword.jpg");

		$this->user->register(NULL);
		$this->user->load();
		$this->user->step = "REGISTER_NAME";
		// TODO Check si el ref existe como usuario, o poner NULL. FK.
		$this->user->referal = $ref;

		$this->telegram->send
			->text("Escribe tu nombre de gladiador. Procura no equivocarte.")
		->send();
	}

	public function boss(){
		$this->telegram->send
			->text("¡Aún no puedes invocar jefes!")
		->send();

		$this->end();
	}

	public function inventory(){
		$this->telegram->send
			->text("No tienes nada.")
		->send();

		$this->end();
	}
}

?>
