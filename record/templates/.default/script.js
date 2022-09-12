		var s_list = [];
			s_list[1] = "Неверный запрос";
			s_list[2] = "Ошибка запроса";
			s_list[3] = "Не выбран город";
			s_list[4] = "Ошибка данных";
			s_list[5] = "Не выбран специалист";
			s_list[6] = "Не выбрана дата";
			s_list[7] = "Ошибка. Обратитесь к администрации сайта";
			s_list[8] = "Введите фамилию";
			s_list[9] = "Введите имя";
			s_list[10] = "Введите отчество";
			s_list[11] = "Введите возраст";
			s_list[12] = "Введите номер телефона";
			s_list[13] = "Введите email";
			s_list[14] = "Нет доступных дат для записи";

		function get_specialist ( id_get_city ) {
			(async () => {

				destroy_date();

				if ( id_get_city == 0 ) id_get_city = document.getElementById('record_city').value;

				let rawResponse = await fetch('/local/components/record/ajax.php', {
					method: 'POST',
					headers: {
						'Accept': 'application/json',
						'Content-Type': 'application/json'
					},
					body: JSON.stringify({id_v: 1, id_get_city: id_get_city})
				});
				let result = await rawResponse.json();

				if ( result.error != 0 ) {
					alert_error(result.error);
					return false;
				} else {
					document.getElementById('rec_specialist').style.display = 'block';
					document.getElementById('record_specialist').innerHTML = '';
					document.getElementById('record_specialist').innerHTML = '<option value="0" selected>Выберите...</option>'+result.content;
					$('select').niceSelect('update');
					$('select').niceSelect();
				}
			})();
		}

		function get_date_rec ( id_get_specialist ) {
			(async () => {

				destroy_date();

				let id_get_city = document.getElementById('record_city').value;

				let rawResponse = await fetch('/local/components/record/ajax.php', {
					method: 'POST',
					headers: {
						'Accept': 'application/json',
						'Content-Type': 'application/json'
					},
					body: JSON.stringify({id_v: 2, id_get_city: id_get_city, id_get_specialist: id_get_specialist})
				});
				let result = await rawResponse.json();

				if ( result.error != 0 ) {
					alert_error(result.error);
					return false;
				} else {

					if ( result.content.length != 0 ) {

						document.getElementById('rec_date').style.display = 'block';

						$( "#record_date" ).datepicker({
							minDate: 0,
							beforeShowDay: function(date){
								var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
								return [result.content.indexOf(string) !== -1]
							},
							onSelect : function(dateText, inst){
								get_time_free(dateText);
							}
						});

					} else {
						alert_error(14);
						return false;
					}

				}
			})();
		}

		function get_time_free ( id_get_date ) {
			(async () => {

				let id_get_city = document.getElementById('record_city').value;
				let id_get_specialist = document.getElementById('record_specialist').value;

				let rawResponse = await fetch('/local/components/record/ajax.php', {
					method: 'POST',
					headers: {
						'Accept': 'application/json',
						'Content-Type': 'application/json'
					},
					body: JSON.stringify({id_v: 3, id_get_city: id_get_city, id_get_specialist: id_get_specialist, id_get_date: id_get_date})
				});
				let result = await rawResponse.json();

				if ( result.error != 0 ) {
					alert_error(result.error);
					return false;
				} else {

					let result_time = '';

					for (let i = 0; i <= result.content.length - 1; i++) {
						result_time += '<option value="'+result.content[i]+'">'+result.content[i]+'</option>';
					}

					document.getElementById('rec_time').style.display = 'block';
					document.getElementById('record_time').innerHTML = '';
					document.getElementById('record_time').innerHTML = '<option value="0" selected>Выберите...</option>'+result_time;
					$('select').niceSelect('update');
					$('select').niceSelect();
				}
			})();
		}

		function get_time_rec( time_rec ) {
			document.getElementById('rec_suc').style.display = 'block';
			let sel1 = document.getElementById("record_city"),
				sel2 = document.getElementById("record_specialist"),
				sel3 = document.getElementById("record_time"),
				sel4 = document.getElementById("record_date").value;

				sel1 = sel1.options[sel1.selectedIndex].text;
				sel2 = sel2.options[sel2.selectedIndex].text;
				sel3 = sel3.options[sel3.selectedIndex].text;

			document.getElementById('text_rec_suc').innerHTML = 'в городе <b>'+sel1+'</b> к специалисту <b>'+sel2+'</b> - <b>'+sel4+'</b> на <b>'+sel3+'</b>';

			document.getElementById('rec_check').style.display = 'block';
		}

		function destroy_date() {
			document.getElementById('rec_date').style.display = 'none';
			document.getElementById('rec_time').style.display = 'none';
			$("#record_date").datepicker("destroy");
			document.getElementById('record_date').value = '';
			document.getElementById('record_time').innerHTML = '';
		}

		document.addEventListener("DOMContentLoaded", () => {

			get_specialist ( 0 );

		});

	record_form.onsubmit = async (e) => {
		e.preventDefault();

			let record_city = document.getElementById('record_city').value,
				record_specialist = document.getElementById('record_specialist').value,
				record_date = document.getElementById('record_date').value,
				record_time = document.getElementById('record_time').value,
				record_surname = document.getElementById('record_surname').value,
				record_name = document.getElementById('record_name').value,
				record_patronymic = document.getElementById('record_patronymic').value,
				record_age = document.getElementById('record_age').value,
				record_phone = document.getElementById('record_phone').value,
				record_email = document.getElementById('record_email').value,
				sel1 = document.getElementById("record_city"),
				sel2 = document.getElementById("record_specialist"),
				sel3 = document.getElementById("record_time"),
				sel4 = document.getElementById("record_date").value;

				sel1 = sel1.options[sel1.selectedIndex].text;
				sel2 = sel2.options[sel2.selectedIndex].text;
				sel3 = sel3.options[sel3.selectedIndex].text;

			if ( record_city == 0 ) {
				alert_error(3);
				return false;
			}

			if ( record_specialist == 0 ) {
				alert_error(5);
				return false;
			}

			if ( record_surname == '' ) {
				alert_error(8);
				return false;
			}

			if ( record_name == '' ) {
				alert_error(9);
				return false;
			}

			if ( record_patronymic == '' ) {
				alert_error(10);
				return false;
			}

			if ( record_age == '' ) {
				alert_error(11);
				return false;
			}

			if ( record_phone == '' ) {
				alert_error(12);
				return false;
			}

			if ( record_email == '' ) {
				alert_error(13);
				return false;
			}

			let formData = new FormData(record_form);

			let response = await fetch('/local/components/record/record.php', {
				method: 'POST',
				headers: {
				  'Accept': 'application/json',
				  'Content-Type': 'application/json'
				},
				body: JSON.stringify(Object.fromEntries(formData))
			});

			let result = await response.json();

			if ( result.error != 0 ) {
				alert_error(result.error);
				return false;
			} else {
				document.getElementById('form_client_record').style.display = 'none';
				document.getElementById('form_client_record_success').style.display = 'block';
				document.getElementById('text_record_success').innerHTML = 'Вы записались в городе <b>'+sel1+'</b> к специалисту <b>'+sel2+'</b> - <b>'+sel4+'</b> на <b>'+sel3+'</b>';
				new a_toast({
					title: 'Успешно',
					text: 'Вы записаны на приём',
					theme: 'success',
					autohide: true,
					interval: 10000
				});
			}
	};

	function alert_error(id_error)
	{
		new a_toast({
			title: 'Ошибка',
			text: s_list[id_error],
			theme: 'danger',
			autohide: true,
			interval: 10000
		});
	}