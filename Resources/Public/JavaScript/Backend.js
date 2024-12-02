document.addEventListener("DOMContentLoaded", function(event) {

    // Toggle send buttons
    var toggleCheckboxes = document.getElementsByClassName("toggle-form-button");
    var i;

    // manage admin forms by checkboxes
    for (i = 0; i < toggleCheckboxes.length; i++) {
        toggleCheckboxes[i].addEventListener("click", function() {

            var parentForm = this.closest("form");

            var sendButton = parentForm.querySelector('.btn');
            var clickedCheckbox = this;

            // Toggle send button
            sendButton.disabled = !clickedCheckbox.checked;

            // if one checkbox is clicked: Toggle other form
            var fieldsetId = ((parentForm.id === "form-odd") ? 'fieldset-even' : 'fieldset-odd');
            document.getElementById(fieldsetId).disabled = clickedCheckbox.checked;

        });
    }


    // safety question on submit
    var safetySubmit = document.getElementsByClassName("safety-submit");
    var j;

    // manage admin forms by checkboxes
    for (j = 0; j < safetySubmit.length; j++) {
        safetySubmit[j].addEventListener("submit", function(event) {

            if(!confirm("Wirklich abschicken?")) {
                event.preventDefault()
                return false;
            }
            this.form.submit();

        });
    }

});