<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Receipt Printing</title>
</head>
<body>

<div style="text-align:center">
    <h1>Receipt Printing...</h1>
    <p>This dialog will close automatically.</p>
{{--    <div id="installedPrinters">--}}
{{--        <label for="printerName">Select an installed Printer:</label>--}}
{{--        <select name="printerName" id="printerName"></select>--}}
{{--    </div>--}}
{{--    <br/><br/>--}}
{{--    <button type="button" onclick="doPrinting();">Print Now...</button>--}}
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bluebird/3.3.5/bluebird.min.js"></script>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>

<!--IMPORTANT: BE SURE YOU HONOR THIS JS LOAD ORDER-->
<script src="https://jsprintmanager.azurewebsites.net/scripts/cptable.js"></script>
<script src="https://jsprintmanager.azurewebsites.net/scripts/cputils.js"></script>
<script src="https://jsprintmanager.azurewebsites.net/scripts/JSESCPOSBuilder.js"></script>
<script src="https://jsprintmanager.azurewebsites.net/scripts/JSPrintManager.js"></script>
<script src="https://jsprintmanager.azurewebsites.net/scripts/zip.js"></script>
<script src="https://jsprintmanager.azurewebsites.net/scripts/zip-ext.js"></script>
<script src="https://jsprintmanager.azurewebsites.net/scripts/deflate.js"></script>

<script>
    var clientPrinters = null;
    var _this = this;

    //WebSocket settings
    JSPM.JSPrintManager.auto_reconnect = true;
    JSPM.JSPrintManager.start();
    JSPM.JSPrintManager.WS.onStatusChanged = function () {
        if (jspmWSStatus()) {
            doPrinting();
            //get client installed printers
            // JSPM.JSPrintManager.getPrinters().then(function (printersList) {
            //     clientPrinters = printersList;
            //     var options = '';
            //     for (var i = 0; i < clientPrinters.length; i++) {
            //         options += '<option>' + clientPrinters[i] + '</option>';
            //     }
            //     $('#printerName').html(options);
            //
            // });
        }
    };

    //Check JSPM WebSocket status
    function jspmWSStatus() {
        if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Open)
            return true;
        else if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Closed) {
            console.warn(
                'JSPrintManager (JSPM) is not installed or not running! Download JSPM Client App from https://neodynamic.com/downloads/jspm'
            );
            return false;
        } else if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Blocked) {
            alert('JSPM has blocked this website!');
            return false;
        }
    }

    //Do printing...
    function doPrinting() {
        if (jspmWSStatus()) {

            // Gen sample label featuring logo/image, barcode, QRCode, text, etc by using JSESCPOSBuilder.js

            var escpos = Neodynamic.JSESCPOSBuilder;
            var doc = new escpos.Document();


            // logo image loaded, create ESC/POS commands
            escpos.ESCPOSImage.load('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAAEsCAMAAABOo35HAAAC/VBMVEUAAABQGhPRFSDWEyDe4uDTExzy7uv4+fgFBQXCxr/q497esLPXoqbbESHZEhvMFyStxLfJh4vHh4wkWDgFWh2etadbfWjWm593kYGIoZKGmY5JbFbCdnsSUizfDh/CfYK+hovCbnS6JzK9LDfHFxpUdGALCgrDbHJRcl26VFu3Vlx9mYg+ZkwRNxxnhnO5MTu3V15ggW21TFLi7N+1aG3FLzo6Y0m8N0HBPkhxj33HYGdvh3g2YEW1OkPSeX6euqq6P0fBSlPHVlzUiIy7X2ZniHTKKTaEkIgrWDxEZVDDN0DCUlnLZ2yyQkm9Rk4xXEBMYlS6ZWslu/Rjemx3gnt5zeaUko+zXWIlSTE6W0axLjggPyk3V0NjY2MgGxs1NTWBoY338KrbMThJb1eyTFJmcmwrLCwqSTQWFBRJS0pUWlVdaWHjJCQ0NDTOP0O0Z2363i7UVFZeVSw/Pj4+W0iJ1eczUDxMcFqLpZbZ2Muz3trxFR3cTE1EQ0ImJSZGSEeZvKf57sb25Uq3OEJUWlatNT3gZWnmdnhZXFmvRUr475Tgfn/kpqboLzQ4tuLbUFD26XKf4+0ZbTssVjtQwuz24Fj05GZOtdFjZGJ3o4lzzeZhTh/36ID34ErpYF+6BA1sxOEtfFCrZGcXGRj34V6RU1K7TlbmREH16m1wlHxhttH27XLjtKh0FQ/y30dhZFpRjmvQ0NENEA705Ib066McHx5m0/CbGBvN0dPtrnrWVSCLPz/27ZQxMTFDmWlWWjxd3vDztEjvs3ndklmq6urO09bkiTdeLS+YrDZHRS/RER8AAADUEB/OEx7SER3QEhzWDx7LFR3VERvOFBnVAxjRBRvRAhPaDR3SEhjMBhvXDiPaAhvZARLUAyHODiPGGikBMggBTCEDUCj5BATCJTHVAAkBVRnXDhnRFCgfUTK9HCjGDiAASBcAIgEaVjPtAgXKBRLNITDgAR0dtOUEayjjBREATwwQSijYHS7+5xIJuPT6siPS19qCvJrP1NZ9Zu1KAAAAynRSTlMAA/39Cv0OBf0WFyAr/f3+Ik5E/v4vnjpfRzu/Z/7+WzR5/vj+p/2Ft7uvVNf+gPKjjccjcfjj6+J1oGnq5m4/282tW5iJ/i34zO/BkNDV8aiP/nRHbR6D8tn8+cyG/tRuWOrQlVPs6v6xlmT+5ONX/bb+zr9Z3IZfTDH+2r73mlM0/cIz76mWcuF0f0j58MirQP7mxNru3Z+Bhv6Q7L7+nPGj6su+s+28mbjaaP7gzL+qk9OfTrH93438z7Z3+vnn3LGfgPLs5/73vbNu+gAAT6dJREFUeNrsmk1LK1cYgOM5ToLmYxo32YgmUZBkk5AqihDxCwteFdxUuMviuv0R0sWFQv9DF/0xh/M139/JJiESF1Wh0GXPmcTaaEylLbZpfe7lXmfumbkzz7znnfe8SeKdd95555133vmXMvNI4p1xJJOzs6lU4p1X8GVl78OHivhdre7v1y8HrC1nEon36HpGcaU7xDbDIDAdx/GxWq5tZxLvPCWzwSEYAgFUIFQAYFbUap7PJ955GlkmJi5FEiwY/uRkba+STLzzRJaFmf4oCxGEKGVUh0rrPPHOqKyylIWRwIxhAGEunBEQfJtOvMj/sroolimWv4iurmxI7kKhSsgjKOgsvvRKFLXG//F1WewzhCnjWvfr7zKFQiFf2uu3fCSgCr18QcjsD5VS4b/j65Vl+MxvslRrLzmMmnorS5EA2PsvHLTWvo9qjbWF2cT0M/PaxCJGLrSlLIMDKWsm3nXQMZEEWHWxNeaY5bIFgX1/Xa4upxPTTzq/Xip9I+rKV8niVOOwK2QNyG8MZCnq+XNZYk9+hShMU3IKsY5qlUJiepkRpnYaW0dRZKmb9cIrZNE4m+fCRnI4eUttR6eEUHNlYeyD2AoB5YgwpsCe/cvOtCYuOe0yP3zq066ZzUJutr4tTL4VESZtjAnCWu5+b7iaLjStnEsRVezG2JxUNQFBD6zeTaksedGZ883bW8U1CMLYx8A7SydeI8snSu/4pFRaXy9t125gj1ExL8v5cR52yohS9IDxS2kKZckrns3Xj61bgHlciIu/3Kx1+DpZyHBdO4p83+za0jWmN7WdceMXy/eKOP0DbvZi+mTJVFU6a1+HOZUIUAwmWq+8POlmxKsvlsUQZRgCCVSYgG+spce6LXs5hgSK+FPApk5WPP/q37YDh2rE5xAoLhcGyPU1yd2Kl9xkWQpmTCMyFCVI/Mh9wjbOMy8kd4PIoTcQSRR+OWWyRB1earYCXdOY7ipZ2rJCBxFKCHa40SlOlqVSYQgjwnQIYhQqtgi5+bj2vH96iLLyPYmg1/jKjGUFUyZrdvnHlVYEfd/pKQQGYefbva02iTMP56QtFngTZSkYU4dzx/MsMwzDbui4BnYcaLWryafJva0j4QpDc/fzKxMIWTCoT5WshUbHtnI5oiFMeRBtVg/SyflSXxeyKKZMLU2UtRPLwhqJdi8v6/X9/cpWn+YQ7nG4dFOfHRm7vmIq0hU3O/nEFZeyQFhNTBPFWpBb1RjRCNFaR9XMINo+iPtCAnq9M1kWkLLQKjC3EzHzcxf9nqoZjPZ4WUTl49BCzTOYcIWBJ8Ze9WJZ3Upiqji0IMWIO7oSbeUH+V6U4Z6CfEYIVvOvkIU1eL+deFh8171r5LpUgze/D5tk435pVYzlwGtmhCx3KGuaPhkSK5aPtkMQpVQxN+fF9mDKXEPskFVoNdMTD965RogiF4vIeqwPOpYsPAmwt+Yfx24TQCnnhmt1RDnyEFnhhylrPYgpx5BAA9cXw85BSu6jhr7U/yExWRZWZCHOoXn+2FU4smQJhfXu8WP9sNYmhGBMdadfSjzK6u5NlSwZHauyi0417WfZZ5G6Sn3sGpqxFHxKTz62BB9kHYojJWKfBZCAP8oSlXu/RZgsKrL3DfmSfJSVHH/mf2vrOb1lQSSgBr/Lxy/IapsB6jJgfpyUseI+XiyLYRCeJIbkmxaK9+W6X6eHwzLHgUopxVQJagvxKa/MgayzuZcbav/CXupM4tz35d1xvx1VE3PLH8reEtaQk7WOpbtXyKJoNdw63D4R7F9tmDoVCBPW7nCOze+JYONYoHgng5i5CqQso7v1XJYYnz+8urra3yn+Cz9NK2xYLkNIZ5q3sn/WjiLAKMIc3ZxdXuTFxPwjWQKieHjJhwAgy1IYwY6PCOwfDo5N/RgoGuVYcrN7sSAVnMayoH2cGdNKPesDanGsdvYW/3XBlapYOYoRow5s3XpIroWxwIeRdbdSefl7C2L/iZA1BEMwgBCNMEqV+9qweXjehy7FQ6z2x9NSZv5sUMGbG5nnrj56BiFy5YS8Tv0f7jynxzQDepxT+WZHKqCYujz+6E8FkJvW5nlaDJkk6xEcwzHR3J5zXRom947BsO/TeICfdQJT3Tw96iEBdDvFZ6XMcQgJ41iAwL3f/CeDK3n+6UnbRWwdtoCuI00jhqHrg4cqmwOUie0waoo0/6IsNMJAlpOFALXPhzdfC3NyTY4QY3LJrRmGcWuLbQGWskbJfLINpmEHS13MNex2PflP2Upt33Q3RrvFcilS1lxdJnnX5cizfNMAwhXncUNBsVcWX4wsDh6AEAJVjVtabhi0y8KVJLkV5gzNcbR4ZvG4VajhHCMy0qjSLoqzjD43hfktjVGKFAAIhUF0WvyHbK213SXz9GmimG3YioExplR3vNrp1WkndBRmaBhpaHUVBisvzYXtltpqRZ5n2bLlYNt2t2taN+2NrerycB1z0c6yVY27q9SPLNO0Yc5ByHGIJmVh1l4YPXFyL4C6nMcqNE3TWdKJn/U+Zf4JW8nzfs914U1zNLZEXvklNBBFgnDjQMTf4u6qmzNWMWeIEJYzy9Xxiau4LimV1tbWTk62Y07W1r9ZSD88hYtOT0G+wynNtfdLjZUj01agYhgUIwFWnsr6YuNecalMV63ND2flnkJ8BOyzN07zUkm6sepkfWLA8HT+SWWzZWKCBIo+6CQnLzqhohEepxqXOMHuQuJPkKl0Ik+FEGugVUkmUpnF6tetyAKKrg9kqQejNWix72iyeoXRWSaVzO9GOTEQWqLsf0vEhSQr9s+5lq8RalVTT3sPXpYwefnZ2+qgci7dBVmdoUGa7+W8zYPUuLP+jnEf/c8Wf2i2bd7N9WrDuZTZadSiwDCQhI3IEhQ7jibzAWitxSVgzXIR7Tlo8U0nonC1fwM51TRglevzz3sPEWBIwANxVzGLmxEnQhan8i1pR5/yiT9FZn1788i31oaLdCFwYW2vTQeynvbLvvzaVoUrF3qiopWLzz7jmgat5lsmebE+E50E+SYygs7OmAH1lpA1YPvhLvdVO4eooREvdI4PC3++QTC3fPlD8vfrvcJxoMg1EQNPO7EVU8WIUGwNntlc09I1jajRfuLNENXBlqmIACfAXFket0RNN20YL/O0YCX/cFOl8q2qAxQcNXbSf+E/f76juBlCLFCADDjJ7LDtuHhkYEwIglFFPpzU95aGfczM2pvleBFXmz8tqYQYwBKuZuI96dHUehlIWUzjoPXjb/+y/G0UeOWzfEqM+IvfDBiVdRwgKmUpJ8MTH16k4p/mzzzoIIKx2j6XuWPLNsRAFh7l32geCjOntzl67fSAfXwwcFX4VBmtjBeOLCZkEa5Gx5nHgNxbqazP/+0rjsKxSRgVsvhQVuFjf38QOwfllqMLWSCQRcxCOQRE5zS8W34jWSJXB9lVzXeWvObCwNVC0/b2UyO1fcMGCFGuAaiJufFga644O0HVjGDsvskIN10guzY8uz24nsNr32wux8FVb0GXIKovebV8oti4toCrY+vubd6HoqN+1AVZVaPgdrcwuLaDmg3cqJocWe3fYYKpo2Ho7c6NnmCypdTs7Nxnkrm52dTrvjw49ytv1x7TVhnF621vOwptgUqhFDoGZbwcg8KACROmzKFug/FQ5zan4tx0vh0+MD4SnfEVIkqCokbUxEQSo3/pHybGGP8w5d7eV5/0ZWsnr4obRacYE893bxEuhWmp84QEuL3c+/V3f+d85/y+85V99YFzFE5KjVfzl0854FWr7edvgOFB9IQCibSyVo27Pk2iqC2cxdUWS9H/1MmVUeghOZoiVXZUZQle4MBIBhOv7CkbfMwkaaFdpLCm8M8BSNmsP35qj+66N06cuDlmhwy6rlPHc9Nl/9Q9mHLTgTBmOvvLDbFG3smJCczkqEfEr/3NyViQSX1oDfaBM24ndu5MiuT/sC3b7ZgJ/H7Cbo7F9Lztbg0SdW3h5ajJu4KUAumBY6W28ovPbPIbc2+rNhypa2+bCwWDobm5Oa1gcyH+97amQ0c6dlY8f6PiIuKwvKDE3HD+OXgVWB1gUaME5j4DhYKsfAbUR974vqW0qrDP/r+sxipaAz7M5aQ4bKYsttJVG3ZoQEyBJKJELsoed9kYemLCaPT4Adb1KJWi7+robboQAnymT1xRqttT2XXq1PHj3WDHj5861VVZrSs9dDMAlx+arivt6MqSCU65JuxpebXCwseUC2K6i/PuQNSSVfnVgv7qPcA/37wzfDV7ye0DDaFhJo1WzlFfI9xvS8COUYRF4y7KELkK8D/gYlVO+8yuLcp1OJVVWXpiGth0ob30scbmdJlinSeUkqVvfKxXeyEImB7SdaXH8UvEOACLdMHjozjPbqHY2C+sEFnV54TCrCBDculNAR2eJOWEHNhlDJQIXrnDhjmdrNQdhuRZ/CbSrve67IGWkrQ1KSXb22hoDwXz2+sMlRV746tDcY3I/0GO7kidNhQJ1ekqblwfL5SKMiyAxeATEKRiHc28lGiaDGf+X4s8MEU7pCQBYEFOYA/XIq+c8UkZp1FtK9qyNNwHbpItyVOeInOeHI7FvSFF1mMGbXBu+pDuVK7y3zR+L4Gm7O4qbpqeCzZ1NKajw+t2EDIkQUqnlnLUKpuKsQLVjBP/Y1NS7QzHcBRLCPE8T17lJ3GKdEJ2kPl3yrA9XCaPldOtBYq1oMqt7NXOBdtLu5qFE+OAungWJtc/ZmgLadtKG1Pg4Ho9zJCl4j4oHoSRFPlIwgLw2Vv+x714JVNqNia1qe2FLYyHpUhK6tufuuQB11zrxqCtY8nioZLlGNqCQW0xeJKAwEYqHcXenaenI/mHivVrF4utPpULhCBbfUzISX3JhxHkBEk6+JD/P5lst0fFkTxYHEN7cY6lCM179fzkIqQRPo2JDufFIxWDqsKQ/2N+k04vfnkjgFWUtoWCWl1W3IUE4UrKsU717J3wIspMW+wYTYLkwZ1/YO1Qd0maJFLMfo6iEVggiWAUVGRG90uvLg2g5DyaGqX2oi2ytUaU3tibH9QaclKSbT0Q8Ml6rHcuX1tasUZdXhLw4BaSNQZAwAarDRAmmmBYnAnHgwWmbF2zkTp50a/cr4Zii7YCv+DbpGUKSlbBFPsw0NqgQ1LtvgXYHgdVdVMoVLcn959JpfjX/eP60rbItAGC12Wr88GwfUpDGy2zu0oyM/cV2aUkCh6Y+/rs+OvItrT4kPx9KdDa75WaGCtt5eklnf1oaU+SbN8MiaEWbtxxS55stePIq+tCobZK+cWRqhntk0gGR/t5lx/p4Y/1j/XXjA6uSy+dNnjhCn3cVasCGEa5mKl5y0zY4kXSGkEzKs8aAV5ZFvCpbGeyLwla2fUe3MVZeV+kpIFrlrC6kuEwJ81xUsca4qn+ulCwqTpL8g99NIPDYxLJyEAfD9zwMD9b9kT7+gZGV/yhTC6Cq8KgjWjjLi0v8zvULsaiUkMujwtrsCbjfHkcrTab3UYjZ/RdkgoI0pgGj5FjCJ5ZKjuaCXn3nOVME/MUp4J1wdXPP7d4OtikS18Pqpr+pbc/+PW3EsloJwJLPvr11yMy+IuRzr6+zqEVp781NrgqFynWBtt1q2pjeVUYJiPUSE+TQkerC7PHqQ1pH4XduJM2sc5L4oioFm2xMcyEEOWltt1pSKgqs2CaCQvFmewNGatOTy9uC2or14MKqDM8MNbPv9g/vDjQw4OlAJKB1Ugky2At1VE9nXBcDJfeAOGwUSG+Q8YuN05ZaZYW6miS8dyyWTwyxZZ637yaQeKIxt5ySaRm4NYtNhWNloAZklKdg/I01ezHTVNGkpOeQ3mE6J1kXREM1enXd8DBgWjn4lANOmE0OhQdlgNYiG2Dfd/2yZSyFcySCwGnrzM6Il7WBW+qborkG0QPBOTmXW6HFCNJK02g5X6bpkSMcWrVjE0FQioJFb9F7a+9FNRC1df5eRVJ0iglhVz+o2yzTw2MJzn12V2r5sF0XXuwaU/6OuPoGRtT9iwODw4BWnDKUOfg5539gI5ENjYKjKvpH+77GyxZz/BwD2A7NtC5ODAYF+qziqdDdTkitACN630eO8cwGD45Px8+qBArvrtmKZyPvBzSpOfrC5JHZl1ZiyIJvsnPQlw7O0G5WIrCHQ3ZotFKKg4F54rFHigm1XD6YDTapxxbHFKCx0UHolEeLOXAMLhj/2DniKQnBlZfNBrt7JMPRzuji4vDyvhhVhhCc6W54mPZB3eFf3PCTo3z9eUZolcyPwrbVbE5fdJKcri3CLTmSwNXxna7kzP5+R5G1oHTLNoW6L8B4pcoWmlD1+WsBxVEn8XPawAiCFTKkShwSz460Bkd7In2SGoGhmSjnYODnaOIWT0AFvjotyOLI4OLQ98ORL9eHFOukZ/tbI+0da26XUrGTbBb4868VHHvQWuRz6meBHdARlkx0n4geeFGcY1ybTGkNuxRMwRvJIhHBEOp3GaZCKvcpuCFYvn6DwHIMvD5yOgQuFWfBLg1+vnnXwOzeqLwE7wysDj0+SL6NsiDBXAOLQ7A1/Bi59eLAOgaD1XfFMqvFo03/kf0vWD/rE8F7sdRfOin1I7C8qRLbNRmaN685jqfotXjxI0EMAvtUyVJbtJmVorOq3gj2FYpuxi1lWPRzs7OKDjWmLxmqBNsMTqq6ANfhOPCK/A10A9g8VMl+B86hF4AeOMNuKybu1CatVYDxcrbHqy3q6SoowdCv5WCvh57Q548eR9U7nd7wzfAleJNdvUsRljB0P0o0qYpU67U4JXV2pAh958K874e3voGZRJl38jISE+fUqIY7BFZvwTAEiLc50vHRgZl60SMnPZIU85FY0pGC+WQWmO7Rgkax22Fran/yR6AgEpttWsObF6DXIqDMy6WpQk/Q07i50AqXYlV+nXB6ZhDJG890TGU3g8M1/zzkCFViax/Z0gSWwsdGGa1kIIHkjQ9U5b5nwxUZrZJcRY32cKta/QZy8q8mBXA8juNdk2raEjK3l+ndf9mCEKttIyy8LvYhSBB7YGD/WN9/LlgF+0QTy8N5evivSo2Cb7km+dIK6+a8EZPVQl+k3zEmnERfpIlcffsdoALTAzJ7nMaaBT1q4DICnEpGGlqTObWq+BQKhCT/53ohDLU/GCpci20Ug+G/RqKpgkKJHkorqnlNf+kTb7bhsOGCUincBy2WrZmo6GIu/8CUpbC3NvzRId3aoO9WUlBleRum51twd70eE/Y8pLGo3ZRftTvStAkg2QbyluUmfyeFZQezKD9cHBpeA5SzGa/5aAcDou55ZWiXTmiP2ucjlyRktzjUshlScURfVPkUK74AlC/ms5hBDLKNcn6J6Qm2uknWMy9W540Vmid1C2IG8gsJC61zN6wGcmcou4jm68wT5wyaC8YspLC6njHI72f7clNhpqAVtMqtArO2FRWK08q1sVhpGfewkBGDe1IrStz/g9g3Xoje5Z8Ko5AVEVQUbQVdNqzgQMg7Ik24J65RSxuVM6FdIpksFLcc+v4+NzCwuuVyaCVWydGC+0NmjDGOuYpjvIEPnpoFkcb3QnftX87YmrJ9rMHNqIDFmz3ssKnyPmnMOhpBxGUw52/BMy14GPL6XCBuPMYgqsuKQ+S3b1p08lN2oXXFxbeSgatLMNqbsnNsxiFjMHIQMMWBQiEOGkhWLV3f4pAq80tszajY7c88btVeUyMkaYt5B3XwoalSZaAWK8hMdz7W8PmFeeJ3BJ4FSlObho8Or5pfHzTha/ef/+r97OSEsANvzbpRYPLvMUjJSEbhbWCK5WIR/VeDGoPzjVVgh5/6gdheJ3AAxsQaz5wcCYr7ZxgdrxwdaHPq6YBKz/q+mcd4SsLltW4lcPRa38sTjJt+fILsE8O//n+5V+9/jFcMglugSeKg+dBjZO2Yj779hIIFHyg0UxpGJeL372d8dI5QmrltRNl4olD1awdA+VMZZ8pebWsyE9htJG1gGOCNuMvqiqIHx6qNXTJbjO7/+cnn3zy3rf//POrrxZ65UmRtLkucoUog5CZz6mM7h2QAwmmPOBQEaSVwjXmtNaic1ILH9Kk/tYNRNqbijwANYuZfCWSgqvDNhYYC61ZBFzPM7+jKjMOK+2vxYokwUq7/+cffvjhl7cX/gRu9cIjTi7KBw2ivCB7u8N3IGNZos4IO50QaGiaKdLY1E5CSOptRRuZEWvP+6SwOck5r7kTlMUWjZshIe2iaVSq298rapWL64ymSB3E/mQ3WZ/9+Zefz7698OuvCwufwcybHLfaI8Xilf2ispqVUbbMraJJv3WStsFbQzvXhI0h5pqNNDeE3SoLY+HUGmgZVdY2aBwkRfEXBVXbVijilsIQatcnXzp88wey797/6rPPPm5O8looQQ7pRM9is7hqzD4DnUF+krNKheUqChnuOLMRakGbnEM1FaBYyndDKvj4TQ1TPs4FaFn8FrVX9AkdsuIQH06TtZff/f333x98rV8p+0/K2z35+V3rjwq1/dhJC5oiaQEt2OdGBMzZG4sm2S3QyM26rCr3gTT0ZK7a5fbiJsbptE4WilTt4qC24j9ROhBaD74cu2ryVhxpEyUQqxOM3d4Vm41pltH4dpTIE2tUEW0zxic5SjVla0njJVlz2MMxLk5tMytW3LRCG9Rt7M2tVjIVgBaAddma5yRuKYbIdbJ1C/a08hlCkAFBN0XrFvP+A9cIkCYMmLAlwKamTBqn2tFQgI4qMsvD816cFlXr6XWRXvmGkIp/5vLb33ntiXXOSfwGMLRgqXwdFtfWu6EwiSnxhFXDOXbsUyaYYeVlpCqEYfLFsjlAYwzHquzbY5pi5kc7PL6SlUMvjYAmkrAJ3aXPd+d0d9+4AoqMV2vk6Tn65ZPke7srKrr3piQOl0D6UHX83yEnKQvYjBzj5HVAhiQwR7gsO9Fb7Cucuf6GO/PSFEvL3FdOOdWQzWOwthYbb0H5yt2zCl2kKT1x7gIhTz3zCJTNYHfd/eLfaUdZeP+n77++Z2lPYXfHocPfgx1+9PSL6ZIN3KY6NJ0T31qXWl7oYHEQ451owcWPSSfcLRkJXz613o05HFNh+ICJTLlA1/JZJ1TqLCwZ1cbepgKgXH520/mVid4FEebF+xBSWzdt3boJvh1bEmUe+uW9txcWEFjoq+vw9z99H4lEAK6ftp3QZSVOL3mvOEYIWsx+twc3WZBBIsRoVL4ZM9LOE5VmprAJlVQ1b7Pbd+x/bl/mjXCj8gAHEZDFLLGFCfFC5hUQQxOGau/RY1vHlw3wevrU32A9LIAFSfjpbd9v+36F3dyRmxhccO7eptCqh5naCmvqLCXEdoZiWZuvhU/BErS0620gKELEw3DMcXZeFag3V9W+uk9DgGhqUv8SOCi+JvyiC7aDGJIgVPfchSC69a5nKo92Vb557NbxrSfHT959I7y2DBZUUOCAgNbNHZU7G3dWnr4Z4XbYkKNI8HYVc6L8ATpsGAeLxCyBWWqpu/DK1A19TpEdNDGYIlBdQzAMQ9ptE+cL688T/JQh9U/tVopFtdxpFBISi1UdT4+DFHPrM7mK3KM1uW92dTc/tQmR6ylYlV3BrIrDiFanK7L01XskzzZKlF0nELu2ndYnRC6UBdalrxTMA2oXR/pjYEmJ2f2JRytkKS12lwvlZyqM5AhAy6IyqiahfEZscxIacO5ysc+VBosTY5Xs1H0IqpNP5Ur2PnOssRs4dndWxdFj4Ivj98AJV8fAkmQ9imil0zc33zz+U3Fzx+kchVyA6/CbWYmV1G2iGVFu9qpBnyNQnwOO28PCJ00kbgdNKhItU1h8sD6P4xw1MQGAEbFqwAKG+ctSVmoN+e1ZiUCV8uIjW2OsOn7s1rv194yPH8t5/OmTj9ccPTm+aSvErSWw9j4KsBxprjnyk+5Zw7bvTzfvPPFol1Je+eg2FLuq0xOaEYNtECqWU+1CO0lb0J543BZoQWvIG2tumFQTaEWeuf/+2Smfx6jCORPkIJC7gfHpm8tTmLlC72tbHTwvu+gM2HXfJgTVU7nKU8dOHtNXHBs/eU/z0a0fPt5xslEPjIMoz4P1erXiCDBoZ43h0Wd120736498f/jxGt304ceylF1vIHadKM69+M3Ek1DQIGqaKmSt8H5Iqa9+i3yj/3TFTKpAeYfsY+KOV1492FIUsFOOX4wqQoo+W4blmYV7d8tWpqOHUkSjShXfR7xrp/EYsAqo9Lzk+F3jW19UIlp1Kx8Zvy8dfnrzaNYjkHGlPPQDSh3eqoTglJNTed346ZqKbdtylLpGfWlz/5GfthXXKDuEzOuocv0tPDAU0XrPnLZ75allZ1Vql9rmb8led8+UUvYPHZBFk7SVX9om3UUfZSgLHriyYce1Uw67VQ0mgOVC/9dlORhcEBNL+RdrVx/T1BXFHZSWFoTi6LOlDvx6s0hfZ+3QFVw7YVXrGHTBaLtWKXVaPxBtaGqVKtXN1gmkZARCM+yyyQZENpfpsuhMFmeyJZSxqcAQVCofgs5aMUNZ9s/OffWjWITqOH5A+8d7r7+ec+655/7u7y5YwAj6Xvyvog4XbXGDFW1nQSi+uo2Tvtl97DBz+/4N61nb3Mdqil7lsPe53dvShn/46ZNPvq30qgV7NR4Z31vJ4exQwxK3wEsIIRZV2FapFENwEaptCbTgu5EvVwrHZgfsOm+sUkdbYkR3KhL0Gh+puPeyJxaYpyweiGm+ADkLEaIvXr71FdoLGp20LvmNfuC3D3RGgH+FtY11LPlTXkxPXpoVRw3y5SiJQRf+2Q9f/6qIi9v2qvvY+oTtee7Nh1nbN0fmJawB36opcguKFKKt7i2vx+1MSTHzvSbwI3OlB+N6CYEonc7BpCgWpVQRxyjXpEuFfA+Uq2q5IIEWNNumszWVm8Y+FyunVzZm1J8/0DPO7Mb/te5MK5wbMfAObcL+yBfzrjaHd4HSOOT4iLDW7hXv7iTnM1GzJQvfXT18uQV6y/3AfX9SwOSwns5YVFvBqMWeOZNKe0Rcj46zO+qLoV1cYLGVlhYOvV+TlPnerxs2r2dtKorcfDi6ZIu7qOaYu6Zmc81WxvpXU/yLaiYRm4vxrXovhim9NeBXHrVQalWoEuCSZtMDvdCsxfUAl0epEaaspD7ZzJkgVpgIecLTIAh6VwW+x1h8aA7Ua8F5Kipp4RvD7W2wUX/eJDTTGclzr06PgdqWLG4vXL7a/1iojxa9c9Gh94dBDjTQsYLmqBT6tKZ6X1/xqMWQZi8tLbUZHLrpfWAjOlep3VHgK8wqazIMd785i5F56OsNJSI2Kh1wqLHYG8Qb9onX16STgWTWajkerFJjJbx8Z07uNIqIY/KqhaKUFDo9V7yXY/R6TWKpWEMAXgShlPNqxGw2W4xzYSYJ4Rq8e4m5LLCLBA0C9MGCsuqMhW/2tLS1gPgBFJuT6YBTZ7011HYxHFVVSMFpOkwv15E52++fcesWxj1xLPbNVUG1O61pTllZVmGnz9fn8xUnjvjA+kam6xwum63wrxUGwM/ROaLLapIYpo9+lZT5DoTml19ucW/lbFYUudPz3GYReXEqTkiNaqdSyCYiTY2sadKNbKlQ71XK9kq1GKEXWq27NYQalzViXCXEIxhgRpC/6BUpjE3mIEEhrHcJc+yKZxBSc5Ln3u0A6TygFiON4qVRkxVCDMniuR3h4b/7O4fzI7q7lq5bG3RxZPQl1/Fg7CmZC5PtpXZLfXl5QTGy8vLy+vp6XaGuEJCyO2L6RuFnRmrxiqy4zIwC36jFobtya13Zjz9+uWHfekhea8jLSIVeudljcuqFKSavnjKNRXjAk9hGQq2RaTGNh8jDtWwn16RSyXGMy1epKpVgepWcK0vXCmvY8MhjDXW2BBOMnaCXlNrePhgTAfR+GMZAfLx5aDIG80ukd929HA4Koy3IwmOuXHiTZLM93YkTQz067tXKsiyFhYWAFiwBFpSD6Swum91uN+gSfZC3bC7dQIEhs0xiGekrd1nqfT6dRDLscN2daz/09YIfd5JTaIGM68Vwb6VTuZuBm4B25eRD4O2GcbDS8wBzOuGHV8nDtE6BkFeZw+fzlyxbAibncjfKa7aPK4bLvrOMMX5BRZ3xdnb/0FUQxgP9uK4WJIoGmSjs7qKQjuxYlHq149LFFpIUcikioqN9aVJwSSO/zXvmBq+yBRnLLaQ5DDZwNLtL1zriKy6vB3BGfOWusmiJpa/PZ7E7ivuKLWWSHpdtYMTmGrHZ52aSYMlUWo9aq3TLtYSGIYZ3Gp2N+kivGpOKtCpcSeBOJ/cmRJ1SLkuxWsU4jyuX8+VyrmJ9AuVZKUZFzmKDBz9J9vvXOppbw1Bv+XfQ20PiqDDGhc9/kx7SrCR6Ueq99vmoe0HK9YZd7v9w7M44VLzfedYMmrEnPz+/6fRpu83lcjksuvJi30gxRCNytgKdLTNTYij39ekMNld5X59OIspaYbMl9hlsIzr7qC7a7wYeAQSiU+3lak0mLTx0unpjI6YhvGqT0CrS7lMTSv63OI9vgoyl1puMWGOjU5zO5nASVjJF48KFBkR+0KePlmS81XP5Wthjya/WVnSGQSdIslwIXeJn9ttLr3VCJYrGRXIRumdx0piHwG7znlWI0A7kHzlVe7o0K+MdBzhXPRgEY0HBqM6wILMpywFIJVpsEJaA2IIoyepUu8Hnc7h8BfbC8If6ahQjYVV4eVql12jF1OnwZq7Hw210YkpCzRYotNbtQshfLy/hb/wWw3hcLk+B43iNMPfw95tY409gUNbqlY1Zz5+5buHq2MEBEENtDjRoR8wP77raP2/5jNBbWxn915pj/VounSCqdqU/UN+PuSpgKBzft46A1VZU1J375cyZS9d0htKyzFKDrgDCT2crTcuO6etbMYc6M3s0rakQsLLdu2d3+dIeFirTWGrCKndrAC1CnCJnoapOD0UoT5vOERGRhJorY++VCrh6D6FWVnJxWXpKAoslYoJN0Krp5VMDy8q5/e1tf1yIhSp8DFZhse3dQ3OTobP+PCsvs+AIj060GZosvFr6gY0TkC75E1yLmn/q5Mnq6oOkVVef/Pnn07W15x1nihFSBnupDZyquDCjLDpjNLU0E+ggLnvMoM0+4HgyWgs8GrPSzdOqoYI3a0liDGZUadgwUjYaoRyNJEzgYFazVqjQ5On1+krI7jgm0EqDgzCg1uEErt4PhIUjTjykqCdQhTVfbe9Ykf12HO15m7+0pOXD3c0g99mcmBjWHni+GV3VK54woGnUA/mnzp49dfbk8ePHq48e3QWLp/+eO3f+/OnT5y0FPl+iQzIz01CemlVmG+nTQW4ftNvvIdn36LKZUimHOY0mJDRauVeu5Xm8xkaElp+zTN9twp3mdBlPTxBofsjdJjBb94IhJSnqxFUk/zYWqNc/PNjc1QpJ/fdLLWSl1Apj2UD4vOXkhO0FlhUos97p74biA44jCVB3grrhxjLmJDQccC6/b+0CO/pNXUUtCsu6b3b98++586VN+efPFMS+HTdH54sxlBb6VtgcvkK0kJuWeuinT3IQSzLXY9LiXr1ArPISOTiHRsYOw8pHBahRlmu1sneXcE3KBw8IMLWJhwnMLMqEHye3d1lAio967UpYF2iCktbS+lv4xY6e1OXrZlKCJuehe1dc9l+XY2CXWXIgEvLb+GTQMw7syS/LJ21PPspegNMuRGWoOl5RWwG//3y66cgvZwoMkowrIxYovgxR6BkNxXc/fdiD30R4MKcHEpdYD0UCbrXKtuZpZGaOWKH0ur1Kk1EgZYmo9JUfPbS1gMQkrnUnPTAO28K6SMn0VqA53utu71k+Zzb1/xDLXyL3BiW2/TlmHGXlkHed1KgMEYyMtbUVVQcRTuBhdQ0VDUcRaEcbKhBidbW1MAD8AqCl+Ql5GcXz/WChJKP38iAUPWIrrvZ6MKmWS4BbcdOlVgGmUYM/QRhuPZzCYtIpL4XGFOnFAxWR/ortuoQ0KiLgUKUVqxclTRp9IVWp7y29tpw6xp9zQunrMvJRzqqurgKra2ioANCqvkFEmYNV1Q11u5CTHQfADh49eu50GcU/p7I9Bgv+0ter9ZjT6FFjZoHRQ/DEVgFX6b1NKHGZ2GzV5m5TGDV8+YkTPKHYzAyF9P3KkrUBXa3Uu9MvgC5Zx+DQG4tmRU2ZCFL0gkByA40XKtOWygDfAoM4rKhDHkUiBRn/IDhVRUMdpDSU2Cryya/CD1YYtJUfr0gzt+lVMrNCrcTNMJNWKtGU0GhSR3o9EIZCs5SJdqtQKDRaiCuu1wPYPhRgs3W2dfS/lZFJnyreTvBlmKtupod4aRrjwJGGKjJbkfmq+vjxk4BOVV3tqYaqgwfrKk6dbajID9jnlJb42afxfwsC5qKbNMYU5mE9odrOYB42EUpjLmtNrgYFoQeC0rTvu01rQxVLmrb7FSxw5eJWx/D7yXEBzd0ph4ssskJlzYjQoFiFDIbD2lpUSZyEeISS9WR1XUU+6XcMBpP5uOUtKfz4xOefax+5ApPFolI4uVIKhVMi5+WuZLDYJVyukJ1iNUMQKrhylUl1QpHOooXKE1lCD2wBLoZDu1+EcBIytqgUloV8AwrjgIhKGno6GBkPoLkbxOWeA+gdypqSvKIdO3bk5e3LXUm2WmdGM6VSMoGxBFz+kpxly1Q42y+3tXZNipQ5jSTWcKSih/t8wEIvIOmVY7iA9BdJ6TTq83DH6Buvw1RnCranQ3Wwb0Ok+5HtL6Ej2s4jJju+qjf+xn2wG/GkKMhj5fPgrzd0wwNXLl6MxTQjOW3OrJ0zoqMYtEmvBFSLmzCmTIEBVmghHwyYNGCw9LPyMfGaver6jfj4+Bs30P/x93tJUvtL/3uzW25vJfW54m0cYYuejvbY4dTVb2S/+2Fy2gJJZtzs2VH0ZykRmnt5U4PVmv1uP5cGsAKuA8C1Gbmsn1YPID0yBNh9Pn1KbrnqZen/Oq4flWejsHXwHhy10Tb4Z8tQbGzPrXmrsxd+EEcdN2Xd2T0lwyylyI/Vo0CMhJc7/CzAhJfvI5AgAsl/YPGw4DAFRlW9Ag8fihNR6FHRM+OSooPpfu3TW0CDEZ3SAhSHcDh4ZLCto+PKUD9IQgY331Uvc6bEsbajyEOEkP1bS0oUiAQBf0rI+3FvIH9axsN349w7JFrxMBudipv+x9uV9DQVRWHt47VSKCg44FAcUBFF0ShS6xBARSKCGolTBE0cwSkxLoxjjLowcWPcuHXh0ujfsLR9fXSgFgpqaCmgBIgiS79zL9j7KBoWtx4RQkPa16/nnnfuued83xHjsjC6UYY1a0XezZvbip3VdVWOsrLymprilDOZ6oANzG/MJliBSIYpxxaNDpTQ7tYYaPJPZkoB6yywYVg1sgvFYSsHi/aewKp/zVw2Hr2hNs4W4lcJ3eMoAfw6j6tPdaIV8/bse1tfX1lR3tuTOzwMMZvBbijb+PalnPY999k0P/iTYBozEhZqxw9bR6gBaBk3WN/OW6SsCAYW4lTjJNlrU3PbHAZWRml8bFfB5B2vwM7DfIEMsBb35x8Qnifv6IX1JXUNjoOAyOMKhmCBDpuJDAPnntz2lNPDzGdhpihFYLWTTfBbgMAVXNJTtGosdLYqw6wTnnWVXw59v3ide5a5NL5G+PyXMtey75QRKTNLhR0P5nlGol2xWFcoAJYKnwJT1UkmA6aM4X2SsvMrCrj5OEZY08l45Z3myWm+vsRqcMNXdnp7EozAQjPpU+HalzS3MLC2l5qFRxeOMbDuSGEiarXvFysPPRDbwpeLKZAZTMOokilUP3UVLYIws0oiXXAq+hNtAiqYL6zbBg268gtq8y/idwmX/YiD1SimT60PZiF7t5aKXkRgwbfyl0gB61j+UgGsPT0IN9Ssl2oaDIqc5hS2NbeCIIXlGujEirXZeKefj5NFFIYqs8QsawgzTRIMbnSF5QtNIgYHXm99bL37cr/FULMbmy8n0eIDt2dEZXQCyz0tWB4GliNravP73uJDzpIn9ZWOg+UjiSAasoIwRUHE9yQiXp2k2IUu/FOyOGMftK0l11oiutY2R+X77W8Wiw9tsFN8j2+Rs/jn9qPpQSg8QMcFwBgXIUUtAlAzdRkVOQ1ZRjZlGc6qZ6fLinpzOztySLUK8DoNicptfJeTaLF5gbYWEZk994bRU9ooVs/Oz2erUIo/U8xNnvHMhoyDq8/v95B1d3tYR6jHqypuxRbp64hAn7p80bQ5viHvWJS3/ISjx0ss3V5jYxf6k2WVyTKvsxS++aIYRK75UfwTw9OOIQbWOlnM9kvFmJtdES30kV4kcnEYqS4jHsUCXsUzqKBLN1CTNzNJF+vKVfQ0Xi1QmSEWG6kyJ2kjfQXLkO8Ik2ANG8HCLjGOAM+6UKTYwi/C3dxa9UlxQS6xPRwOQDRR1yDrkru7sv5JtdM5AJXSPqKvntmG+2i3CrD84bJM8RCcbuGSrJXnpdcbkxF3Clhb7MyvJAwVJ5PqhcIiqguNj9SUlxeVOapKnHW6ouvBohcWniN0+rWcxAyJHmaTUozN7dXDRVnJ21WtvVEeWJYjHK3mJrycANYB/hu5QRxYzbez7F1SpLQn71BQR7iAIsuL7ExWQ5xXM2oKj57Gq+Nf9o2fup4TnTErhrmBRNI83LP4kz8d6qcsUpaZqfJAN0WOlhEssGF9hV/hi3xBGlhDl41JiIBczSg0Onq5qFCW46fJrzCFwJlZfUBFNt8ngLWY2g0lXvrxiR3inBbikxbAIvbR8wyr+K81GRJfsTG/dm5q/YrT+wQVXetL8CZjTFCbvErIOePPvbJLJc0KhzmZk35Bq5FUtFoYVqgqN5kpQb6m3/rxhjzLutA+xrDq3yn1BZcc/lv9b16ZpoLhO8Fb4C11MQKreqaHFdmOGIEVq7L8ealzdKgr9eItr1n9j1WVccHjqwkscIaVxmn/bI/j8FuqLdg1HVUAHthb8QnjXRHd7eQzydUMrDrLvyqqs0WJtLBGYFUL9aChI/guF62mKwyttW2XWo8vGh++Nf/N3AXrhrhbxXdJiu3C/bx/uspY5r5eot/R3KtHqwgKvgzVYJUxBBy/SSV3syUl3cpq6FT9LsxaC8p7W1hNWbJdZGN1FOovve0dgGd9qJ04qBgClbxks25PadTAB7a8oTto87t8nj+Kn3uLOk2aKuaYXAOml59VrHceWrk3D7jhYaYB3R3BdLQSSSZmKJfIB4uaGuBc/MiirfndR4DEi6Px2gL5KkzmUiOPCCcZAIMpNjp8iveTY1l2dnHZILq31MDuTCN7a6IjFqMSYbsrd6SnZ1VF3frNR4tPlKwK2jQdCvbRpGINTivyN8n3LBodf9jG1yL+b/xlZ4c6dghUyDfLGTqzMDKY7u7s1Fd7GFjUz+4aqCgbAJWbv689OAWsleOFGu2MclTVQ3WJUCwUTSQSIX9ORxiajqYoEXClFSwY5loxmTJxfvh94xd41vaCjLS8FAdLZDAFfRYor1ykqJcgkjA/JOCVduyw+6ClbDYORkdww4QRrXQEA9I5NsWk6xDXx9A92KUKE5vFokP+jvSw8gOulmaKW8y+f768SSCvTCNY5j27I8Sw42IIBAcTbi/et6pqWke73w3pCWPMqvb5qbHS76dCPByRpuh4nZScUg8U5Ql/vC49YE1SXlxhcYudY+wUsEojWMWJEIDxuWm2Iidw435AXY3zB9C06e4O0m6ckmc90RQvGYeH11NpUJrUBMDNNrhvVno9S7QCui/O4Seurf/Fs5zY1BAntTuSqxd+OrRyFEovrMkUeYAvrEZRUzeAFVY1YpEitIQuZzq7QGH1pyMr/WCJI+eAayJJfcDjezrBwhanC+mRRu/8N3HXGtJ0FMWvbe7vf2mtljln6ylluR62snykYeTmgywKe5AaPZaaCuWntGlCTyqM+lJERI8PBVkZRPUhgqgPMSorH1gWbCvFEpcfXEoRnfs3uzl3w9n520/BF267v9177rnnnPs7rwMbjgaPXtIYKJFAf/c6ssXr2qFyVYvizZu6ujrGE8xD0AukShEh9awvJ8fAY9O196Y1/9cJKJc5RNi7IYtjvwqU5skLuDRNHaxpXU1UB+QFyC83jVG8X+PlpC392FjfoBgloV9W/jnV2WwKed/MuOL4WcgQrtfWXk8oyO8LRuTqCQOqn8UiDWPbnkkDVjR8knINCz41K6hCTWRgyJt3C1Ve6YqM9PTUrrGfmxshVljXVk/RVBf4ornh7dtUlr1H9eD5VRlC7Y8ntSaSAKoYLD6PCrXRkPAnWV0to8BCKV40fJjVR+aaGS1toMVT39Y4HYbvBXUwVEMsWjQrE6ohltFqiK6unpCQntQZ8zOYQgSTuS2UZ2YF9JP1/fu9vYSIhTOlGKqGYEMTK92lYzXLDYoQuMrVNfv3pj9+zsEZqamLaQeav41VoLmKcVMmrd++ftqUsNHKwSPa4lwtA1nhun4BdZhZ35/she9poozlqxERmjjgID01/cvLkLqGJZMGbDPB48OCxaFJ77E/4Fay8QVQvm4DuhhZUsKakmU1EwbceBbrTKxo/DQnmA2WU2DIpY5PXrhzm4hPVrKlQ5sWAwkcShZdhgIRbEH5+fY8gTCgRUpDB2gUtzQsmyTI0uJQ32k5QPAR0wqRBknr9PGTJ/eqqP8T+Try9aZBiSiE/NvAGLxq+fT4YCIPzBYnWnbHu57BYwS2rt+7B7shOVZEdZZTkCcWy+4wLGLdZLCRjZQ3HKyfRtkCWUpKlhpibN3A1eIwGWJn8+AuzQhB3ObEPe+weLU0t5L3AllVSVtdMLFKqOQGOq45mE8qN6KcE+V5ruhEWsFtufW49tG+IhegRBZTIkR17hghsmBHZ1U0yAildqvVE5siUdWdwZGXw6miwQX/YpVRSVDBxH2NEEzuqPniAkxOUcszJLEdNl25wa6EcVLSGHmLGO3cu4pyF9XGEGR6+5O1m5VkpBCq1a6TL1a66C7sgu5u/GbFTFJfS82IjMCuVuYr6W2l3tWYmrkRInsObDOycuTIEiIcsj3binTgyv2xpsPhiJKJLVWEg/mJ8iOmI0IgsuBYD+Vq6yWnxzChNVYvy5hAsleOB+b30cgSiRw4VkQ9hurrVWeNUOXgYTs8qu9Lby6OHDSJ7clEBmRSrnoOH4u/dmv/REOrwUPLSNHJioIQEz6yzdxbaFo5zguZPbAESyYpM0ABV0fitB44++CzJWx0ylGrUWkjDPIFS1mVMizBlGBCdjfuOaWj5VkGg2PnOuzyptAsWpzlM8z1D7bFbM0HQ+g7pGUwohutMPAZ3FRhRdgNWjQ66qBGR4dHs+QOWnzmtOjbIyo+Mvyb1wV2bmYFAg/oG8puV7mrWqoDyfiy51Q0qijFEATlICRotw0/9poLKU5YBBxdhzjkUsnjW93unr6615Q+stj9BUyojBwnUSi15w/H15ZeYwHNFpRKP/hwHjrTCC4O0UVIAIwsWZDcnhXKbUNZPEwBBphYAODaJzQWLW6hpxKEOE9AVLSfrP1ykMW0aAZfwNFYaRldDvGzeZRSc+BOjm3mTLtUblCac2cHk3lm97RANwAT6hKXa5/q18y6IANZTA56C/GFI7COYGr5GY4otBZXBNn/QFBusXW10juM7YhF3Q/FEjc17xLiYWYhO73McbBkmQf+Ssw+sG5toS3fLiGvMkcHCm9D5UxjkwRPflMFsNvMg5KHlnZUsyJUs5kV33xONrLivNuTqG9W5PcXOPWjoljn30UtBlqbMbg5epoD9+pApttVlNR3eSf+s1xkEaVRm+BV56K/bJ/JZgd8hc8zwpDZV0o1BoyrUtFHEn+lwyjiug5SfiIAPhZcoGThpvKYUGnooJVphTEywMhz/Ot8bWfLkHHldWpw4u6HSeWgSw1sgYhQxrVb0nPinwsjOqJ8DKX4T7b85CqACuoE8dYgqzyKQB2McKgbzFaYJDVsKqM1D3r0SHm4llpaX2wF+c0Vg42RdYZDiWaDRY/rau2GoENqpkBE02gTnAyjLMYEJLpYswGjyvctPjbeQr+Vby/3GTtpFQZwPRbUXCvcCKmGuTU5XimUaUxEBEUVhxa3/lY0sjYW3MmR4++gNBW/y6vtl5VcY6kNx2VLRaN/5dUrqkzJcRtBqseiQzbvTk7CULzMTLTN/0M0rULPy4MCfs5hGqCi3gMyxoFv6i6qnmY0dHg6I5DDM8o0Q5xv+7sjt38dweTwtyHkA/DTrEeUZJcVHuA2CeAJUVnM2BvWaClzH1nT0apFu1/BVDez1L7/clvylYopZfZcfoCe988VldkEkF1o5ZcLaxKZACfaiEjS4u7y8lE1pXosrthKACPrGwX0oJMjrisA81Ph73w+U2nubx6TXXBTxVd1TsTPk+ie1lyAtZg+DjuevPYrr7mgmGfP2wWj7Ltp5We8VGC3seCLKHCNQJQjBvntF+Z1OjwPUyZDJDCJoEKM5fYFP1B6hJZKS8FZ/epKv32HofUr1BlYT2SsomXP1237qw6nul2pK1AnVoKBK7+l5EwOFOBviEyPz6ONUZWVmcIyioqOhiG/VD4Lf+mCjnkwNaBGaszzjDFEVVVWRsj9b99OCgSvjtSZpib/F1AjAgcITFA9mNk3bylJ1fneGyY0rvRZ7FQoNwS+JJwzAddqQbZ107kskaiv9t6oIkiALuVpAhkh6Lnp+olO5HyrRNZFFVFd7b2iRguQGtp1I1dzWyFyzxDI7gOoTGzac1Ekphu9JwkSoFtfFGfJ5GRjc7XDTg/jXEFZsAbYZKnIT+6uLdaFIAzPMNm1dVuaVtu1QdxKGxRZqhVemrYP0kqlScU1clwqEkGCB8WJh+bEgxAnBC8OCREkJ/riEoknifudCLIbIUvTiJeNN7OjS0Iv290pPb4E65y0s/vN//8z8/3/zvRs7V4LKMA4obzOKnhjkeLNQwyAcxjbEbmsKdVEebqWhd0QrO3e2oMofeEiovnVdhn9wajCt4roPHWABebJtMk6fPLUt5Mspe9DQu19mSDR30lilUpDKHvAk8mv15MfUzaRjBpg/4yeqkp1RBx69sTt7m5sV3TA+Gs7IQar75NaoOQXhjo9xNglZuPKWrMIEhIgPdPaf+nbqbX0pqM1R0IIua69OLxgbN9bdEHIUqhmwh5YlVxJtpWrK9fSw/5z3UfpceWK1iwXXtnXR+Qr8mdVYWOaxtw6ooumBvKo3muoGj221vZuPbqf3ppQ0Gst/0R8Nel9wwrWYz2YxvKv8FPML9bXikR1BoC0uLp6gpqoyLrLdepSuV/JUXxwhg8AWpvUNt9hwS9Tyl7sv9B76+IJgVqdn1LnvuCvMoUhOleQHll6CUBDsni8+EIUWnQc6u4Zc/byeQDouIVa991IWD0VwtismA5ZK3U1n9Q8MI1efpNkwWG/sZ7ucwjiA7JoBXdZqH/Tet4dPxs2rDg1wWG1ruZvmnKn4XYUuggi21eM0CG8xmF378ZkcZGsba6kcv0XgCA4sEr3GEzXxhKtEMlvvFPIc6CI57oNyNKL41X7VUhzjl6aUyWLt7nzFDb2aFly4H/rkqVTtUXPjRZphSzv+k1kqMgWC41VDjakqmG74f3UUaZKFjpia3yFgEuWU95GvuDBeeQi14UTOnuoKb2+n1kxF2gMrBqF7VpWL7Esh04Wtiw7XAlyItDQFfKruxCpNd6zKktReDAr5cc01W1vMDxzyiCLs7PxLdTTdJK3iT6AiDKrX3Hg74P1lyu2Ig0695MsPop9yDpXbs2EzGZYwb+Bw6+pa+w0f6h3f5UsXzBrwwf9ym/Fo5TzX3S2lI6Jqpux3npP79oqWR6BtRPbMVedDkxSWFUEh1W24P7enipZO/HU26oyGiznvGBAwK3JgmXLnnPhJACErDwOfhYnO6lmJ6kwCHQI2HBCEVxWP3zlOgMO7nNhzq2uQTypcnQR5qrRpKGI/+oQeCU5EbGq0FxxsGdvxkD2uMVSy5BT87sac5Ht64t3CltkUabGrLkis9YBr2E960AXgFZaXqcpTVrGhY368ZxtBc6xrb8BoMl79mtazNqgOMcBzmKyXHEro4srKauhJr0EQQkvCZcdyDLtTOiz+f4WphBOORmwYlyIAWe3xQBvZd7iScmSGf/v11+lXH85DtoJVOrHyoO5Tg5IZWfIknFhsiysobFIJIjKZN5U8Um/vhHxZtBOYG+/g43LJF2upKasc+GrdpNl9E6qLLpNNoejVqELtBW6atrXX4JmB6aYU0lF2mxZBlV8yKlEw2a4gqVSP07vtX/2gN8y31LoL202+QTedZooePFluy0LZaKK6ubNeQcR3oeU2k0W7NuyGcWLhT6zoyLjkWSnX/fF9pGFA3tAUMSkuU6BYDUeC7cY7+i0EfGViJRCr2TMu4dflFOZ1lb2pwlZ5psQVNkZRmYncutX7UXcjY17QJvBGqIpbGFKHQ7Kit+jX9MnC+qJDUmutODr/DES2bvyLGgnrMo/zIzUh4oQYEx/8PTNsNkJCj8jiMOifjxPqx3eKeudPyfWIdz7QXKMDjWy4I/v1eSgR3/+1m6oQ6n6GbrUshgk1tX8NpflfWZUTkcoISpRN9fBT26VLu/klOzMzchS6Fb94yjgjipiNMT/b1QREKepKKl0hAfN7As2cT/WNSMpKqoQ+V+pIiNXQBBl9cjkADL4spKV42YkE4qcmOzr5NDTHBzbREkCKLIjqshqMhznAGiFMaNSOrsslsNfILkHOFVg6JLpQ0EzuoAr409UlMq6dMSFDB4a+53xe+TdmZZk0ZlzR7gBThUAC948WziVAc3DEe9J45mqEz91poswZnBS92h5xrc55j/idCoVYXKAGfBMQTD308cRX97NHm8qucn5wkJCwYxFc0JszbJs/Slt3Bt2r8ilKnK5Ek2viaOOniWZdsKFrwa/HDTo1cLxZssnfBl3zqmKiqxpUX96ZyTgXcTzHCLgeNciryezM71O1WRZFNUNSex8TcPcQGFy1pvBD588Hv508dAWyk2QK7AzLQQTmixWnBiqqokEqqr/t1LBo2dqnT+f8fJsNcwPfKogGPf+6d2Hjx69fG/+tXnjyREfX7YrHErvWLEiJ0lSVIck5Vas2OEOhQO+ONcSD6PGgk4Hiw/Jf/Dw3qQv01rp49+NhWUc/A84HKgGrSbMauzMmWM63bwmjHz84P6jwc8njrZ0p5Cg3k/NY9j0T6++zHaADgZ2wuVvH9x9+/LFu6UAGrc9Dv3tHkZj533FJzl/XtDRpjV0/uuR9763dz2tyRxhfDPjKI3uLnrxImpUEL0oVlEKCSYNKSQxkMsr5JxzX/oZyksJ9DsUCu2X6DcYdnZndtdd113Xi6LoJQqFHLujMWnflrb0r2nzw4Mxutn5+czz/J7fzGYHgymrPk2esjMuxf7Rk94TEjdWWle13ll8d9naE7qzNCPpwXT8Jf9pfd7jqd07jAv/CJ6ka+feKFACrNrObJP5OVexcxMRrBA8a3Yfb1J44RqG6Oar/8Buzb3nJ/GxZ+CBps0Odja0IiWTMVXXsDIgo+MEP+viyNCnvjS54Xqee7h/582Uw/H61lA/HciK0td7+cyOshU5HSmMUYqgrsqs1zhJCqmcSpQ+Q8b1379zM5yqLR+qj2x90HGgpk+BcxgTfoRkWNgJBLF/ZgKi2jNnoku6CofDw6O2DxXcl3qlUJD846fffVfNxCJ/C12RRG1kOb3Z9TbHj/uEUgr96o/+F0/93cc7EmihmoMI0eni8GLuQEllo8VMxpQqtlfJCrHTu/lMIfL8/OKA+zd/cVJP1HLmChSYkTuNbJi5HGGFUt19FnzR02XvLibsAPYCOWpQBeu94OxShw6GlAYPok11WDgQMk3fm0oISdJksmxexv5SpoKwLbsuJATbaGpXH0Ot5kIcRLV7tXFyIvVmbwWdi4iwA4g1eQEikt9dK9GcKVINYwUTMOyETpcWECnW+pTqUHGcSkrY+2tkQjRz2jkILDQRYaZCoqQlVt5cMJHM9yRD1ZB/yd8buxg5kk3F5U7Ux6qPChgD6zC6Hkeq4gCCCcaELjOJpQmCZ4wYjCDJRqKXe65Se2v8EaJCydRHrdxoWBOEomgoCjUJUaXp/DS0Zqu6SOvYx71KQoh8fBgwxxh0nW+Efx8fdKwpopiOtzGTPHYAJkyTaHW/PRQx1oy+Kc/n9gowAzjBCPb+jJzaj9c/auWXQ4eKXntfOACIDkDD7Bt2Go4u1jZttDNME18RnXa8Onalqa2TxTgwvHcA0erIhGBReqrkxxbCmCGnJnTT8kDRdHV5kUp8XG76om4g626jvMLZTLF4cJSI/taq/nP4hWLxy1q7wSzT8xArFKxGQjiSdVtefPGZA4iq903O1p6QPRwCrNiic+YvmGHA9KwWF3YEqXf31l302Vzuqz7WvHw21DJ1VbHTi+tNyB26aX0qLcprzV+ryOl0Gs6bx8UPfizCn/De/oRQ4rZ0PpqlVxaQgcygOBuJTleIj6Y288v7HRMQTdf90nqupubS1CAU9pDaN6A7qu5Q95M8uclsdxBE2wukYhWsToT6mNgaEa1aeBMemZyuMYQrAa+xu68wJkF4GJPJ4qq+vjY38v7uYf7afizxtl68/rB4nGf3jqFBOFAwpZB4K1HJXQvZuU3hsCMkGtaIKQobVcM8bI9do48VlTA7bfrnxcguJPfnnR9P46wWZE2l6L4ZDR86kBLg32wtgMjFBBLNPMsEXHmyjFG/766QPBgu2knejLc6nwconXz00ellMZXlB87eHTYeZsP5l7WVIYkDnxCiKJohubnW59VUYKF+krMJ4HGdqQxggQykRS3KpWnuvq/IRNVsL3e6E9nqlzrZ6znTKKXaPBUEkqFTf7D4+umXtyrCqr7sCiVHpJiSXKWx7BEK4ffct7/1TG+qT22b6hTO5Py38YiQvXFMCtJnHx/NAB7MZEYMLqp655nY9ib/LiaT8yy/fZYrM8WG953opjflRUZbtDM7assHGqfhcu0O7ksR4XbW11XZbcZ+ShaVu9EbBlU2rMTD0Y+bC6DbEk9rtwMoSYwQAACiCnDMswMhdOGAQkEfHSV9JCPTYzMbsQG0WvtrO3qP3+QfsOnyLY/CxgowrQ+sDp+JHzkSBGauu7+bVHGUfagqwUAPk3zw0jQ49Yvn5FqaQKJS0E3OGVGQe8Jf42xhY9rZC8S4zKii0OHQYYAQJq7exYTqMK2pSO7uNzxLPr86PRlqTIG9RuLJdQzIklg9qADx8+81RogGnVqUL8yZulOp72hYcSRvXFHXdXWc4drQhFOdOLfC3vPAkI/prJ6cIaIH/UhM4CHgWUz/Ph8TPpsUmKLiXKVSkV1J7TPgVANXUSQq9MpCtXaZioWE5LnFqIpwXdh7FCoTwOB9cb97PB/S4OPKQJFWnYCt1DIX2B87jFC90oN2mnf8waRzka6Th+LTlcipkU18Yp7F31QcxAh0WsUg18QvPq3M2U1CCNK/b0hudT+ZPGo6RCey1wqlHvpYhdbxvhDZkNP2CooOzI+2f/FiAjBirebMo4AASPSpigHPW5HrXc1WT4iVR0PzXWydoURJ07D4TFbJF4mPh0EQlb8S6YKIw8U7bqy+ySaK32Q3ZKXF27V0m7s6Ad75m/iZhVVwv058e/zxLWVU13rt7eQOyFKoct8DBV00Te+e9fWRDO93w2X4zcveDyp8hYcH0tlAYdAtbxVYsjKkKobDwB+PdYYiZv20eD+66ka3McLJ6s+66/Y3v9IIWFXeJBs9qBGWS2yPUpQDcpDT2JJRtgAmmi4ZTDWXrcu2KaL+BDa7b4S93Q6rzbefuI5u9z6IDLgVPhv4+A+HWNERXbfS2bYDoaHqCLqz84MIH1jomJOlc7LWVZUQ4jXf7Fd6ikr684+3ZCXmhFDdeOCt6Dp+F4AyNkXTVa6ciQrJluOMj7u7Ja1+la5t4zNOayTt3BTDfH62VyLZ1MB1yS8tPCQXAAWGKV9l16p7ArGWFru8Wy4vAMZgchUOHSqoT6byk8ESbTmyorPV7SNZ3RGxsZp23lUTwqaPOo6Hdj1Z/QLCHQtqqu7Ma0epg7YjKhhjfRzfMLlfPJTvC8DGBSB5nQ8C++LKgn09jT8/qHc7C10jGJjfCnvHLtIIHVxuhx8pWdDAolnafiNLTcWie5zcJrVo+AVSxafM+QRQW6LDhwfRRH1MbTgpC29i4U2MdFsz16YqVuDsdEsWX057GFhpe0qInksJwomHsIYG5acycTuTSEBWK/pYYpcaVRQeeXsvaB/NLy5l3PkDQAhErCArikIhL2tfn7c/P7nMcOPp6Gqk2diHk+DlaMuCGrZtSTMYMAwEqVkO5tOlBTFG7ufPGwUapkZoehTfBFLmrEcxILcvk6KfIHqZd6AIBwPs+zMIvuLKvjrxXNu+ia1X/KqLAWFoevZ2TRbxBxSrkoJ1cdDLn0b5NFuIGAPu820P+a6nMCLB4oaseL6nsQerLLx4BMPJdEYDs0cR6E88chfn8wgDGX7PRf56LXsoF/rSw9vgmaUHZJH5XJ7J8ih//HZ9gLcjG6vMbT3Vt0jZggWGlG/XscQdd6iZw/JOrEf8abo+iFfbDVnDcuPqYH/tzI1twkSnFFrn+bYrsj7vhLPrLQDq8iAbj3/8cSIbFjZknfUQlS3uK2wbAZkSIluPDXrsxvPO2sWXohV+x0U2gYFXf5sMb/eeuohNxdEpn1lH44FCpMm7rJDNmwpRrLPE8yc3PoaFdNl63k/IQxBoGppv2sPoXe4isyPrzX8ePy9T144oTSXqN08+uhj3ICNprxMSsjleCnsBWT9xlaOtgCwiMU7Ndo9hD6ACsqqP7WjipRbA39z3zxG7M23J0JThsOf4EBNgPxwInCydk/WeU7DfMpFCROvgeTX+ZLVaDf1GOfSitcLvtghrY8vQRQAAVAiAps/HnZ3TXyIrcuUi1Rcn188fPzqrtD+qJ6L/eaY2CGVKeWBafQSRYTnNYnjd9FGFoF4+9h6z5QHARDJ/pA3eZGI7tFrzd4Mv2ySLneZY9v1xpZR97JAhkMG08j5Zp+m0ed+3Oj/l5wV4Cn9pjQzF3tY3RXKjLheeN5lMbqLvvbG4XJ7l8+fV0Iu6LuAvxvsjzlZPSqWL4/IH779eTyViH4T/C6LzryuSHJFI+L8jmf5WPNP2v81Or3jFK17xile84hWveMWv4wcRXP3CkMjFoQAAAABJRU5ErkJggg==')
                .then(logo => {
                    var escposCommands = doc
                        .image(logo, escpos.BitmapDensity.D24)
                        .align(escpos.TextAlignment.Center)
                        .style([escpos.FontStyle.Bold])

                        .text("{{ $print['address_1'] }}")
                        .text("{{ $print['address_2'] }}")
                        .feed()

                        .text("{{ $print['invoice_no']  }}")
                        .text("{{ $print['reprint']  }}")
                        .style([escpos.FontStyle.Normal])
                        .feed()
                        .align()
                        .text("{{$print['patient_name'] }}")
                        .text("{{$print['patient_mr_no'] }}")
                        .text("{{ $print['father_husband_name'] }}")
                        .text("{{ $print['gender'] }}")
                        .text("{{ $print['sale_by'] }}")
                        .text("{{ $print['sale_at'] }}")
                        .feed()
                        .text("================================================")
                        .text("{{ $print['heading'] }}")
                        .text("================================================")
                        .text("{{ $print['inner'] }}")
                        .text("------------------------------------------------")
                        .text("{{ $print['footer'] }}")
                        .text("------------------------------------------------")
                        .text("{{ $print['sub_total'] }}")
                        .text("{{ $print['discount'] }}")
                        .text("{{ $print['gross_total'] }}")
                        .text("{{ $print['refund'] }}")
                        .text("{{ $print['net_total'] }}")
                        .text("{{ $print['receive_amount'] }}")
                        .text("{{ $print['change_returned'] }}")
                        .feed(2)
                        .text("{{ $print['developer'] }}")
                        .text("{{ $print['developer_phone'] }}")

                        .feed(5)
                        .cut()
                        .generateUInt8Array();


                    // create ClientPrintJob
                    var cpj = new JSPM.ClientPrintJob();

                    // Set Printer info
                    //     var myPrinter = new JSPM.InstalledPrinter('devzone-printer');
                    // cpj.clientPrinter =myPrinter;// new JSPM.DefaultPrinter();
                    cpj.clientPrinter = new JSPM.InstalledPrinter('devzone-printer');
                    // Set the ESC/POS commands
                    cpj.binaryPrinterCommands = escposCommands;

                    // Send print job to printer!
                    cpj.sendToClient();
                    //close();
                });
        }
    }
</script>

</body>
</html>
