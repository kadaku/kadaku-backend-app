!function(){"use strict";var t,e,a;sessionStorage.getItem("defaultAttribute")&&(t=document.documentElement.attributes,e={},Object.entries(t).forEach((function(t){var a;t[1]&&t[1].nodeName&&"undefined"!=t[1].nodeName&&(a=t[1].nodeName,e[a]=t[1].nodeValue)})),sessionStorage.getItem("defaultAttribute")!==JSON.stringify(e)?(sessionStorage.clear(),window.location.reload()):((a={})["data-layout"]=sessionStorage.getItem("data-layout"),a["data-sidebar-size"]=sessionStorage.getItem("data-sidebar-size"),a["data-layout-mode"]=sessionStorage.getItem("data-layout-mode"),a["data-layout-width"]=sessionStorage.getItem("data-layout-width"),a["data-sidebar"]=sessionStorage.getItem("data-sidebar"),a["data-sidebar-image"]=sessionStorage.getItem("data-sidebar-image"),a["data-layout-direction"]=sessionStorage.getItem("data-layout-direction"),a["data-layout-position"]=sessionStorage.getItem("data-layout-position"),a["data-layout-style"]=sessionStorage.getItem("data-layout-style"),a["data-topbar"]=sessionStorage.getItem("data-topbar"),a["data-preloader"]=sessionStorage.getItem("data-preloader"),a["data-body-image"]=sessionStorage.getItem("data-body-image"),Object.keys(a).forEach((function(t){a[t]&&document.documentElement.setAttribute(t,a[t])}))))}();

$(document).ready(function() {
    $(".show_hide_password button").on('click', function(event) {
        event.preventDefault()
        if ($('.show_hide_password input').attr("type") == "text") {
            $('.show_hide_password input').attr('type', 'password')
            $('.show_hide_password i').addClass("bx-hide") 
            $('.show_hide_password i').removeClass("bx-show")
        } else if ($('.show_hide_password input').attr("type") == "password") {
            $('#password').attr('type', 'text')
            $('.show_hide_password i').removeClass("bx-hide")
            $('.show_hide_password i').addClass("bx-show") 
        }
    })
})