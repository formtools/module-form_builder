## Template Set Schema

As of Form Builder 2.0.0, the source data for the Template Sets have been moved to standalone JSON files, found in the 
`[FT Root]/modules/form_builder/default_template_sets/` folder. The structure of these files is now determined by the
`schema.json` file found in this folder. It's a standardized format, defined by [json-schema.org](http://json-schema.org/).
 
These files each contain a single, complete Template Set (i.e. the contents of a Form Builder template, allowing you to 
generate forms out of it that use its styles and appearance).
 
### Purpose of Schema Files

The long term goal of Form Tools is to be able to share _configurations_ of all forms, Option Lists, View configurations,
user configs, email templates - _everything and anything_. This will allow users to install not just Form Tools and the
modules but any pre-existing data configuration, significantly cutting down on configuration time and promoting sharing
of great-looking forms.

In order to do that all data has to be well defined in known structures so validation may be performed to confirm 
data integrity. That's the purpose of these schema files.


### Example Template Set Schema

N.B. I've annotated this to explain each property. Note that valid JSON cannot contain comments! 

```javascript
{
  // the schema version that this JSON file adheres to. If the format of the file fails to match the schema version
  // specified here, the Form Builder will complain when it attempts to import the Template Set. Over time, if we need
  // to add/remove content to Template Sets the structure of the document may change, and this value would need to be
  // updated to match the appropriate structure.
  "schema_version": "1.0.0",
  
  // whatever human-friendly name you want to give you Template Set
  "template_set_name": "Template set Name",
  
  // the Template Set version. Any changes to the content of the Template Set JSON file should always mean this 
  // value gets incremented. Also rename the file to include the version there as well (it just removed any ambiguity) 
  "template_set_version": "1.0.0",
 
  // a description of the template set to give users a rough idea of what to expect
  "description": "blurb here",
  
  // the time the file was last updated
  "last_updated": "2017-11-18 12:00:00",

  // this contains all the templates used in generating the template set. The property names are all required and must
  // contain at least one template in each. When a particular template type has > 1 option, the user is presented 
  // with the option to choose what they want via the UI. Otherwise it's just prescribed for them. Note that the arrays
  // of each template type are ordered: the first item is the template type that will be selected by default when
  // creating new Form Builder forms. The structure of the content is the same for each section, so I've omitted
  // the details for brevity
  "templates": {
    "header": [
      {
        "template_name": "Name of header template",
        "content": "smarty content here"
      },
      {
        "template_name": "Name of second header template",
        "content": "smarty content here"
      }
    ],
    "footer": [
      // ...
    ],
    "form_page": [
      // ...
    ],
    "review_page": [
      // ...
    ],
    "thankyou_page": [
      // ...
    ],
    "form_offline_page": [
      // ...
    ],
    "continue_block": [
      // ...
    ],
    "navigation": [
      // ...
    ],
    "error_message": [
     // ...
    ]
  },
  "resources": {
    "css": [
      {
        "resource_name": "More styles",
        "placeholder": "styles",
        "content": "CSS here"
      }
    ],
    "js": [
      {
        "resource_name": "Javascript",
        "placeholder": "js",
        "content": "JS here..."
      }
    ]
  },
  "placeholders": [
    {
      "placeholder_label": "Colour",
      "placeholder": "colour",
      "field_type": "select",
      "field_orientation": "na",
      "default_value": "Red",
      "options": [
        {
          "option_text": "Red"
        },
        {
          "option_text": "Blue"
        }
      ]
    }
  ]
}
```
