using System;
using MyExampleApplication.Anthill; // Namespace of the ServiceReference to the api endpoint 

namespace MyExampleApplication {
    public class ExampleAnthillApiMethods {
        private readonly AuthHeader _authHeader;
        private const string Source = "Website";

        public ExampleAnthillApiMethods() {
            // Set the Username and Password values as appropriate
            _authHeader = new AuthHeader {
                Username = "YOUR_ANTHILL_API_USERNAME",
                Password = "YOUR_ANTHILL_API_KEY"
            };
        }

        public void CreateCustomerSaleExample() {
            // All values given here are examples. 
            // Please use appropriate values according to your Anthill configuration.
            var locationId = 1;
            var customerModel = new CreateCustomerModel {
                TypeId = 1,
                MarketingConsentGiven = true,
                Address = new AddressModel {
                    Address1 = "74 Azalea Drive",
                    Address2 = null,
                    City = "Anttown",
                    County = "Antshire",
                    Postcode = "AH1 1AA"
                },
                CustomFields = new[] {
                    new CustomField {Key = "Title", Value = "Mr" },
                    new CustomField {Key = "First Name", Value = "John" },
                    new CustomField {Key = "Last Name", Value = "Smith" },
                    new CustomField {Key = "Email", Value = "john.smith@anthill.co.uk"},
                    new CustomField {Key = "Telephone", Value = "01234567890"}
                }
            };
            var saleModel = new CreateSaleModel {
                TypeId = 1,
                CustomFields = new[] {
                    new CustomField {Key = "Subtotal", Value = "140.00"},
                    new CustomField {Key = "Value (inc VAT)", Value = "168.00"},
                    new CustomField {Key = "Another custom field", Value="A value that means something" }
                },
                ExternalReference = "AB/123/456"
            };

            try {
                using (var client = new ApiV1SoapClient()) {
                    // endpoint will be configured from web.config system.serviceModel/client/endpoint settings
                    var result = client.CreateCustomerSale(_authHeader, locationId, Source, customerModel, saleModel);
                    // result is an int[] containing the CustomerID and SaleID
                    Console.WriteLine("Customer ID: {0}, Sale ID: {1}", result[0], result[1]);                    
                }
            }
            catch (Exception ex) {
                Console.WriteLine("An error has occurred\n{0}", ex);
            }
        }

    }
}
