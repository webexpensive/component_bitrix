<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * @var array $arResult
 */
?>

	<div class="context_form_any">
	</div>
	<div class="booking-area ptb-100">
		<div class="container">
			<div class="section-title text-center mb-50">
				<span><?=Loc::getMessage("ACU_RECORD_DESCRIPTION");?></span>
				<h2><?=Loc::getMessage("ACU_RECORD_TITLE");?></h2>
			</div>

			<div class="login-form" id="form_client_record">
				<div class="form-sing">
					<form class="form-inline" id="record_form" name="record_form">

						<div class="form-group">
							<label for="record_city"><?=Loc::getMessage("ACU_RECORD_CITY");?></label>
							<select class="nice-select wide" id="record_city" name="record_city" required onchange="get_specialist(this.options[this.selectedIndex].value)">
								<?=$arResult['MAS_CITY']?>
							</select>
						</div>

						<div class="form-group" id="rec_specialist">
							<label for="record_specialist"><?=Loc::getMessage("ACU_RECORD_SPECIALIST");?></label>
							<select class="nice-select wide" id="record_specialist" name="record_specialist" required onchange="get_date_rec(this.options[this.selectedIndex].value)">
							</select>
						</div>

						<div class="form-group" id="rec_date">
							<label for="record_date"><?=Loc::getMessage("ACU_RECORD_DATE");?></label>
							<input type="text" class="form-control" id="record_date" readonly name="record_date" placeholder="Выберите дату" required>
						</div>

						<div class="form-group" id="rec_time">
							<label for="record_time"><?=Loc::getMessage("ACU_RECORD_TIME");?></label>
							<select class="nice-select wide" id="record_time" name="record_time" required onchange="get_time_rec(this.options[this.selectedIndex].value)">
							</select>
						</div>

						<div class="form-rec-suc" id="rec_suc">
							<div class="alert alert-primary" role="alert">
								<?=Loc::getMessage("ACU_RECORD_SUC");?> <div id="text_rec_suc"></div>
							</div>
						</div>

						<div class="form-group" id="rec_check">
							<div class="form-group" id="rec_check_title">
								<label for="rec_check"><?=Loc::getMessage("ACU_RECORD_CHECK");?></label>
							</div>

							<div class="form-group" id="rec_surname">
								<label for="record_"><?=Loc::getMessage("ACU_RECORD_SURNAME");?></label>
								<input type="text" class="form-control" id="record_surname" name="record_surname" placeholder="Введите фамилию" value="<?=$arResult['USER']['UF_SURNAME']?>" required>
							</div>

							<div class="form-group" id="rec_name">
								<label for="record_name"><?=Loc::getMessage("ACU_RECORD_NAME");?></label>
								<input type="text" class="form-control" id="record_name" name="record_name" placeholder="Введите имя" value="<?=$arResult['USER']['UF_NAME']?>" required>
							</div>

							<div class="form-group" id="rec_patronymic">
								<label for="record_patronymic"><?=Loc::getMessage("ACU_RECORD_PATRONYMIC");?></label>
								<input type="text" class="form-control" id="record_patronymic" name="record_patronymic" placeholder="Введите отчество" value="<?=$arResult['USER']['UF_PATRONYMIC']?>" required>
							</div>

							<div class="form-group" id="rec_patronymic">
								<label for="record_age"><?=Loc::getMessage("ACU_RECORD_AGE");?></label>
								<input type="text" class="form-control" id="record_age" name="record_age" placeholder="Введите ваш возраст" value="<?=$arResult['USER']['UF_AGE']?>" required>
							</div>

							<div class="form-group" id="rec_phone">
								<label for="record_phone"><?=Loc::getMessage("ACU_RECORD_PHONE");?></label>
								<input type="text" class="form-control" id="record_phone" name="record_phone" placeholder="Введите номер" value="<?=$arResult['USER']['UF_PHONE']?>" required>
							</div>

							<div class="form-group" id="rec_phone">
								<label for="record_email"><?=Loc::getMessage("ACU_RECORD_EMAIL");?></label>
								<input type="email" class="form-control" id="record_email" name="record_email" placeholder="Введите email" value="<?=$arResult['USER']['EMAIL']?>" required>
							</div>

							<div class="form-group text-center">
								<div id="record_button_final"><button type="submit" class="default-btn1 btn-two"><?=Loc::getMessage("ACU_RECORD_SUBMIT");?></button></div>
							</div>

						</div>

					</form>
				</div>
			</div>

			<div class="login-form" id="form_client_record_success">
				<div class="form-sing">
					<div class="alert alert-success" role="alert">
						<div id="text_record_success"></div>
					</div>
				</div>
			</div>

		</div>
	</div>