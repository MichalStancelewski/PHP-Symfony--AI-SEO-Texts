var toggleButtons = document.querySelectorAll('.toggle-link');

toggleButtons.forEach(function(button) {
    button.addEventListener('click', function(event) {
        event.preventDefault();

        var articleId = button.getAttribute('data-article-id');
        var toggleValue = button.getAttribute('data-toggle-value');

        sendAjaxRequest(articleId, toggleValue);

        var siblingButton = '';
        if(button.classList.contains('toggle-left')){
            siblingButton = button.nextElementSibling;
        }
        if(button.classList.contains('toggle-right')){
            siblingButton = button.previousElementSibling;
        }
        if (siblingButton && siblingButton.classList.contains('toggle-link')) {
            siblingButton.classList.remove('active');
        }
        button.classList.add('active');
    });
});

function sendAjaxRequest(articleId, toggleValue) {
    var url = '/projects/switch/' + articleId + '/' + toggleValue;

    var xhr = new XMLHttpRequest();

    xhr.open('GET', url, true);

    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                // ok
            } else {
                console.error(xhr.responseText);
            }
        }
    };

    xhr.send();
}
