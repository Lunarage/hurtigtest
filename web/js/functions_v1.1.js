"use strict";

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

/**
 * Validation of personnummer.
 *
 * {@link https://github.com/navikt/fnrvalidator}
 *
 * @param {string} digits - personnumer
 * @return {bool} True on valid number and false on invalid number
 */
function idnr(digits) {
  /* Check if the number has 11 digits */
  const elevendigits = new RegExp("^\\d{11}$");
  if (!elevendigits.test(digits)) {
    return false;
  }

  /* Checksums */
  let k1 =
    11 -
    ((3 * digits[0] +
      7 * digits[1] +
      6 * digits[2] +
      1 * digits[3] +
      8 * digits[4] +
      9 * digits[5] +
      4 * digits[6] +
      5 * digits[7] +
      2 * digits[8]) %
      11);
  let k2 =
    11 -
    ((5 * digits[0] +
      4 * digits[1] +
      3 * digits[2] +
      2 * digits[3] +
      7 * digits[4] +
      6 * digits[5] +
      5 * digits[6] +
      4 * digits[7] +
      3 * digits[8] +
      2 * digits[9]) %
      11);

  if (k1 === 11) {
    k1 = 0;
  }
  if (k2 === 11) {
    k2 = 0;
  }

  return k1 < 10 && k2 < 10 && k1 == digits[9] && k2 == digits[10];
}

function validateFødselsnummer() {
  const fødselsnummer = document.getElementById("input-fødselsnummer");
  const labelFødselsnummer = document.getElementById("label-fødselsnummer");
  const noFødselsnummer = document.getElementById("input-no-fødselsnummer");
  const digits = fødselsnummer.value;

  /* Disable input if user don't have an id number */
  if (noFødselsnummer.checked) {
    fødselsnummer.disabled = true;
    fødselsnummer.style.display = "none";
    labelFødselsnummer.style.display = "none";
    return true;
  }

  /* Otherwise, enable and check */
  fødselsnummer.disabled = false;
  fødselsnummer.style.display = "inline-block";
  labelFødselsnummer.style.display = "inline-block";

  if (idnr(digits)) {
    fødselsnummer.setCustomValidity("");
    return true;
  }
  
  /* Set invalid input status */
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
    if (urlParams.get("lang") == "en") {
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
