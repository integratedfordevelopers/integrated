# Annotations #

The IntegratedSolrBundle ships with an Annotation driver. This means you can use annotations in your documents or entities.

## How to use ##

Using the annotations in your documents or entities is very simple and can be accomplished by importing the annotations:

	use Integrated\Bundle\SolrBundle\Mapping\Annotations as Solr;

	/**
	 * @Solr\Document(index=true)
	 */
	class Document
	{
		/**
	     * @Solr\Field(index=true, sort=false, facet=false, display=true)
	     */ 
		protected $field;
	}

For more information about the different configuration options see the [Getting started](index.md) documentation.