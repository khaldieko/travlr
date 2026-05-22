/*
 * javascript.js
 * Travlr - A Travel-Focused Social Networking Platform
 *
 * Client-side helpers. The main job here is the asynchronous username
 * availability check used on the signup page.
 */

/*
 * checkUsername()
 * Sends the typed username to checkuser.php in the background (an
 * asynchronous request) and shows the reply under the username field
 * without reloading the page.
 */
function checkUsername(username) {
    var statusBox = document.getElementById('user-status');
    if (!statusBox) {
        return;
    }

    username = username.trim();
    if (username === '') {
        statusBox.innerHTML = '';
        return;
    }

    statusBox.innerHTML = 'Checking...';

    var data = new FormData();
    data.append('user', username);

    fetch('checkuser.php', {
        method: 'POST',
        body: data
    })
    .then(function (response) {
        return response.text();
    })
    .then(function (text) {
        statusBox.innerHTML = text;
    })
    .catch(function () {
        statusBox.innerHTML =
            "<span class='taken'>Could not check that username.</span>";
    });
}
