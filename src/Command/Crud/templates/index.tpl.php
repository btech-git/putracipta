{% extends 'layout_page.html.twig' %}

{% block title 'Manage <?= $vars['entityTitle'] ?>' %}

{% block toolbar %}
    <a class="btn btn-success btn-sm" href="{{ path('<?= $vars['routeNamePrefix'] ?>_new') }}">Add</a>
{% endblock %}

{% block content %}
    {% for type, flashes in app.session.flashBag.all %}
        {% for flash in flashes %}
            <div class="alert alert-{{ type }} alert-dismissible fade show" role="alert">
                <strong>{{ flash.title }}</strong> {{ flash.message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        {% endfor %}
    {% endfor %}

    <div data-controller="html-loader"
         data-html-loader-url-value="{{ path('<?= $vars['routeNamePrefix'] ?>__list') }}"
         data-html-loader-auto-load-value="true">
    </div>
{% endblock %}
