**Universal Carrier System**

This provides the basis for carriers to be implemented in a consistent manner.
There are three main componenets:

* Carrier - This is used to contain the other components and to set up communication with the carrier's API.
* Communicator - This is used to communicate with the carrier's API
* Manifest - This is a container for the Shipments. It is uploaded to the carrier at the end of the day to notify them what shipments to expect.
* Shipment - This is an order for a customer to be delivered. It consists of one or more items (parcels). It holds details of the customer's name and address as well as the items to be delivered.

The Carrier provides methods to:

* Add a shipment to a Manifest.
* Delete a shipment from a Manifest.
* Alter a shipment.
* Print a label or labels for a shipment.
* Upload the manifest to the carrier.
* Print a manifest.
