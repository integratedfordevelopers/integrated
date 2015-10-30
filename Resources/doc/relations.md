# Relations #
The ContentBundle is shipped with a Relation document, CRUD controller actions and an EventSubscriber 
which expands the Content form with  relation options.

## How it works? ##
In this example we want to relate keywords to news. The first step is to create the Content Type ‘News’ and ‘Keyword’.

Next step is to create a Relation document: 'Keyword relation'. A Relation document has a sources collection and a 
targets collection. In this case create a relation with ‘News’ in de sources collection and ‘Keyword’ in the targets 
collection.

Now when a 'News' item is created or edited the Form will be expanded with a Relation option where 'Keyword' items 
can be selected.

## Next step ##
The backend stores the relations in an embedded document which contains a relationId, relationType and a references 
collection containing a reference to a Content document. 

In our example the 'News' item contains a relations collection with the 'Keyword relation' containing references to 
'Keyword' documents. You can fetch these relations by using one of the following functions:

* `getRelations()` Return a collection containing the embedded Relation documents
* `getRelation($relationId)` Returns one embedded Relation document based on the relationId
* `getRelationsByRelationType($relationType)` Return a collection containing the embedded Relation document based on the relationType
* `getReferencesByRelationType($relationType)` Return a collection containing all the references based on the embedded Relation documents that matches $relationType

Return to [Getting started](index.md).