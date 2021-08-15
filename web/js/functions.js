function saveInfo() {
  const name = document.getElementById("input-name");
  const tlf = document.getElementById("input-tlf");
  const mail = document.getElementById("input-mail");
  const fødselsnummer = document.getElementById("input-fødselsnummer");
  const noFødselsnummer = document.getElementById("input-no-fødselsnummer");

  let storage = window.localStorage;

  storage.setItem("name", name.value);
  storage.setItem("tlf", tlf.value);
  storage.setItem("mail", mail.value);
  storage.setItem("fødselsnummer", fødselsnummer.value);
  storage.setItem("no-fødselsnummer", noFødselsnummer.value);
}

function loadInfo() {
  const name = document.getElementById("input-name");
  const tlf = document.getElementById("input-tlf");
  const mail = document.getElementById("input-mail");
  const fødselsnummer = document.getElementById("input-fødselsnummer");
  const noFødselsnummer = document.getElementById("input-no-fødselsnummer");

  let storage = window.localStorage;

  name.value = storage.getItem("name");
  tlf.value = storage.getItem("tlf");
  mail.value = storage.getItem("mail");
  fødselsnummer.value = storage.getItem("fødselsnummer");
  noFødselsnummer.checked = storage.getItem("no-fødselsnummer");
}

function validateFødselsnummer() {
  const fødselsnummer = document.getElementById("input-fødselsnummer");
  const labelFødselsnummer = document.getElementById("label-fødselsnummer");
  const noFødselsnummer = document.getElementById("input-no-fødselsnummer");
  number = fødselsnummer.value.toString().split("");
  k1 =
    11 -
    ((3 * number[0] +
      7 * number[1] +
      6 * number[2] +
      1 * number[3] +
      8 * number[4] +
      9 * number[5] +
      4 * number[6] +
      5 * number[7] +
      2 * number[8]) %
      11);
  k2 =
    11 -
    ((5 * number[0] +
      4 * number[1] +
      3 * number[2] +
      2 * number[3] +
      7 * number[4] +
      6 * number[5] +
      5 * number[6] +
      4 * number[7] +
      3 * number[8] +
      2 * number[9]) %
      11);
  if (noFødselsnummer.checked) {
    fødselsnummer.disabled = true;
    fødselsnummer.style.display = "none";
    labelFødselsnummer.style.display = "none";
    return true;
  }
  fødselsnummer.disabled = false;
  fødselsnummer.style.display = "inline-block";
  labelFødselsnummer.style.display = "inline-block";
  if (number.length == 11 && number[9] == k1 && number[10] == k2) {
    fødselsnummer.setCustomValidity("");
    return true;
  }
  fødselsnummer.setCustomValidity("Invalid input");
  return false;
}

function validateRadios() {
  const urlParams = new URLSearchParams(window.location.search);
  let messageBox = document.getElementById("input-message");
  messageBox.innerHTML = "";
  messageBox.style.display = "none";

  let timeRadios = document.getElementsByName("time");
  let radioValid = false;
  Array.from(timeRadios).forEach((radio) => {
    if (radio.checked) {
      radioValid = true;
    }
  });
  if (!radioValid) {
    if(urlParams.get("lang") == "en") {
      messageBox.innerText = "You must choose a timeframe.";
    } else {
      messageBox.innerText = "Du må velge et tidspunkt.";
    }
    messageBox.className = "error";
    messageBox.style.display = "block";
  }
  return radioValid;
}

function validateForm() {
  if (!validateRadios()) {
    return false;
  }
  if (!validateFødselsnummer()) {
    return false;
  }
  saveInfo();
  return true;
}

function selfDeclarationBox() {
  let checkbox = document.getElementById("self-declaration");
  let link = document.getElementById("link-form");

  if (checkbox.checked) {
    link.style.display = "block";
  } else {
    link.style.display = "none";
  }
}
