Webform REST 2.x

Retrieve and submit webforms via REST. Requires the dev version of Webform.

1. Enable module
2. Enable REST resource "Webform Submit"
3. Enable REST resource "Webform Elements"

Retrieve Webform
----------------

GET /webform_rest/webform_id/elements?_format=hal_json

To examine the response format, use Restlet Client Chrome plugin or similar.

Submit Webform
--------------

POST /webform_rest/submit

Example POST data:

{
  "webform_id": "my_webform",
  "checkboxes_field": [
    "Option 3",
    "Option 5"
   ],
   "integer_field": 3,
   "radio_field": "Mail",
   "email": "myemail@mydomain.com.au"
}
