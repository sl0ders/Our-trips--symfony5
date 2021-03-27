/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';
import $ from "jquery";
import 'popper.js'

$.fn.extend({
    toggleText: function (a, b) {
        return this.text(this.text() === b ? a : b);
    }
});

$.fn.extend({
    addPanel: function (selectField, form, button) {
        if (selectField.hasClass("greyHash")) {
            selectField.removeAttr("disabled", "disabled")
            selectField.removeClass("greyHash")
        } else {
            selectField.attr("disabled", "disabled")
            selectField.addClass("greyHash")
        }
        form.toggle()
        button.toggleText('+', '-');
    }
});

$(".addPicture").click(() => {
    $(".addPicture").addPanel($("#news_link"), $("#news_addPicture"), $('.addPicture'))
})

$(".addCity").click(() => {
    $(".addCity").addPanel($("#picture_city"), $(".city-form"), $('.addCity'))
})

$(".rotate").click(() => {
    let pictureBox = $(".detail-picture-box")
    let picture = $(".picture-part")
    let detailPicture = $(".detail-part")
    picture.css("transform", "rotate(90deg)")
    picture.css("width", "100%")
    pictureBox.css("margin-top", "250px")
    detailPicture.css("margin-top", "250px")
    $('body').css("backgroud", "rgba(255,255,255,0.2)")
})

