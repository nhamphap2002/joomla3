
            <title>Commweb hosted checkout</title>
            <style>
                #loading{
                    position: fixed;
                    left: 0px;
                    top: 0px;
                    width: 100%;
                    height: 100%;
                    z-index: 9999;
                    background: url('http://jl3.trongthang.wdev.fgct.net//plugins/vmpayment/commweb/images/loading.gif') 50% 50% no-repeat;
                }
            </style>
            <script src="https://paymentgateway.commbank.com.au/checkout/version/41/checkout.js" 
                    data-return="completeCallback"
                    data-complete="completeCallback"
                    data-cancel="cancelCallback">
            </script>

            <script type="text/javascript">
                completeCallback = "http://jl3.trongthang.wdev.fgct.net//index.php?option=com_virtuemart&view=pluginresponse&task=pluginnotification&pm=2&on=Y6L1057&Itemid=0&lang=";
                cancelCallback = "http://jl3.trongthang.wdev.fgct.net/index.php?option=com_virtuemart&view=vmplg&task=pluginUserPaymentCancel&on=Y6L1057&pm=2&Itemid=0&lang=";
                Checkout.configure({
                    merchant: "TESTAMBBUICOM201",
                    session: {
                        id: "SESSION0002739941863E43055233E1"
                    },
                    order: {
                        amount: "125.5",
                        currency: "AUD",
                        description: "Commweb Order",
                        id: "Y6L1057_0810018001493202985"
                    },
                    billing: {
                        address: {
                            street: "Whiskey St",
                            city: "Sydney",
                            postcodeZip: "4556",
                            stateProvince: "NS",
                            country: "AUS"
                        }
                    },
                    interaction: {
                        merchant: {
                            name: "Commweb hosted checkout"
                        }
                    }
                });
                setTimeout(function () {
            Checkout.showPaymentPage();                }, 1000)
            </script>
            <div id="loading"></div>
            