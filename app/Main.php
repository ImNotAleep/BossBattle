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
		$classes = [
			["\ud83d\udd2e", "Mago", "mage"],
			["\u2694\ufe0f", "Guerrero", "warrior"],
			["\ud83c\udfaf", "Arquero", "archer"]
		];

		if($step == "REGISTER_NAME"){
			$text = trim($this->telegram->text(TRUE));
			$error = NULL;

			if($this->telegram->words() > 1 or strlen($text) > 24){
				$error = "Nah. Demasiado largo. Prueba con otro.";
			}elseif($this->telegram->text_has_emoji()){
				$error = "Será todo lo cuqui que quieras. Pero sin emojis.";
			}elseif(empty($text)){
				$error = "No alcanzo a ver tu nombre.";
			}elseif(is_numeric($text)){
				$error = "Ya tengo bastante con acordarme del agente número 007. Ponte un nombre como Thor manda.";
			}elseif($this->user->exists($text)){
				$error = "Lo siento, pero ya se te han adelantado con ese nombre. O eso, o es que eres muy poco original.";
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
				->text("Guay! A partir de ahora, todo el mundo te conocerá como el famoso <b>" .$this->user->name ."</b>.", "HTML")
			->send();

			sleep(2);

			$str = "Ahora debes elegir la clase que te interesa.";
			foreach($classes as $cl){
				$this->telegram->send
					->keyboard()
					->row_button($this->telegram->emoji($cl[0] ." " .$cl[1]));
			}

			$this->telegram->send
				->keyboard()
				->show(TRUE, TRUE)
				->text($str)
			->send();

			$this->end();
		}elseif($step == "REGISTER_CLASS"){
			$class = NULL;

			foreach($classes as $cl){
				if($this->telegram->text_has_emoji($cl[0])){
					$class = $cl[2]; break;
				}
			}

			if(empty($class)){
				foreach($classes as $cl){
					$this->telegram->send
						->keyboard()
						->row_button($this->telegram->emoji($cl[0] ." " .$cl[1]));
				}

				$this->telegram->send
					->text("No reconozco esa clase. Por favor, dime la que te interesa.")
					->keyboard()
					->show(TRUE, TRUE)
				->send();

				$this->end();
			}

			$this->user->class = $class;
			$this->user->step = NULL;

			$this->telegram->send
				->keyboard()->hide(TRUE)
				->text("De acuerdo, <b>" .$this->user->name ."</b>. ¡La aventura acaba de comenzar!", "HTML")
			->send();

			sleep(1);
			return $this->menu();
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

		return $this->menu();
	}

	public function menu(){
		if($this->telegram->is_chat_group()){ $this->end(); }
		$this->telegram->send
			->keyboard()
				->row()
					->button("Atacar")
					->button("Amigos")
				->end_row()
				->row()
					->button("Inventario")
					->button("Habilidades")
				->end_row()
				->row()
					->button("Invitar amigos")
				->end_row()
			->show(TRUE, TRUE)
			->text("¡Bienvenido, compatriota! ¿Qué te apetece hacer?")
		->send();
	}

	private function first_time($ref = NULL){
		if($this->telegram->is_chat_group()){ $this->end(); }

		$str = "Si has llegado hasta aquí, significa que vienes buscando guerra... Y guerra tendrás. ¿Estás preparado?";

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
