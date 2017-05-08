<?php

class Main extends TelegramApp\Module {
	protected function hooks(){

	}

	public function start(){
		if($this->telegram->is_chat_group()){
			$this->telegram->send
				->inline_keyboard()
					->row_button("Iniciar", "https://t.me/BossBattle_bot")
				->show()
				->text("Ábreme por privado.")
			->send();

			$this->end();
		}

		// TODO Comprobar el usuario.
		return $this->first_time();
	}

	private function first_time(){
		if($this->telegram->is_chat_group()){ $this->end(); }

		$str = "Si has llegado hasta aquí, significa que vienes buscando guerra... Y guerra tendrás.\n"
				."¿Estás preparado?";

		$this->telegram->send
			->caption($str)
		->file("photo", "https://www.walldevil.com/wallpapers/a80/battle-field-war-soldier-warrior-sword.jpg");

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
