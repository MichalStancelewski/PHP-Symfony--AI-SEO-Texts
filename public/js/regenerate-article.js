document.addEventListener('DOMContentLoaded', function () {
    var regenerateLinks = document.getElementsByClassName('regenerate-link');

    for (var i = 0; i < regenerateLinks.length; i++) {
        (function () {
            var link = regenerateLinks[i];

            link.addEventListener('click', function (event) {
                event.preventDefault();

                var projectId = this.getAttribute('data-project-id');
                var articleId = this.getAttribute('data-article-id');

                var linkText = this.innerText;

                var confirmation = confirm('Czy na pewno chcesz wygenerować artykuł od nowa? Obecny zostanie usunięty i nie da się tego odwrócić!');
                if (!confirmation) {
                    return;
                }

                var xhr = new XMLHttpRequest();
                xhr.open('POST', '/projects/' + projectId + '/regenerate/' + articleId + '/');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            link.classList.add('disabled');
                            link.innerText = 'Wysłano żądanie!';
                        } else {
                            console.error(xhr.responseText);
                        }
                    }
                };
                xhr.send();
            });

        })();
    }
});