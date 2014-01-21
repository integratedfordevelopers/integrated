convert
  - class: Acme\DemoBundle\Document\Article
    fields:
      id: '"Article-" ~ document.id'
      title: ~
      content: ~
      author: 'document.authors.person.firstname.value() ~ " " ~ document.authors.person.lastname.value()'
      locale: ~
    id: id

  - class:
      - Acme\DemoBundle\Document\Article
      - Acme\DemoBundle\Document\Article
    fields:
      id: '"Article-" ~ document.id'
      title: ~
      content: ~
      author: 'document.authors.person.firstname.value() ~ " " ~ document.authors.person.lastname.value()'
      locale: ~
    id: id

ignore:
  - Acme\DemoBundle\Document\Article
  - Acme\DemoBundle\Document\Article