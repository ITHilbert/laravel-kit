<template>
<fieldset class="rating rating-total">
    <div class="flex-container">                                                                                              <!-- Hier gibt es keine Radio-Buttons mehr, sondern nur noch die Labels -->
        <label for="total-5" :class="classes(5)"><svg><title>{{ getTitle }}</title><use v-bind:xlink:href="svg"></use></svg></label>               <!-- Ich bin mir sicher, dass es hierfür eine elegantere Methode gibt, aber wegen mangelnder Programmier-Kenntnisse habe ich es so wie unten beschrieben gelöst. -->
        <label for="total-4" :class="classes(4)"><svg><title>{{ getTitle }}</title><use v-bind:xlink:href="svg"></use></svg></label> <!-- Die gemittele Gesamtwertung ist in diesem Beispiel 4. Deshalb kriegen  die Labels for="total-1" bis for="total-4" die Klasse "rated" und erscheinen somit Gold. Das Label for="total-5" ist nicht innerhalb der Gesamtbewertung und kriegt deshalb keine Klasse zugewiesen, bleibt also grau. -->
        <label for="total-3" :class="classes(3)"><svg><title>{{ getTitle }}</title><use v-bind:xlink:href="svg"></use></svg></label>
        <label for="total-2" :class="classes(2)"><svg><title>{{ getTitle }}</title><use v-bind:xlink:href="svg"></use></svg></label>
        <label for="total-1" :class="classes(1)"><svg><title>{{ getTitle }}</title><use v-bind:xlink:href="svg"></use></svg></label>
    </div>
</fieldset>
</template>

<script>
    export default {
        props: {
          'value': {
              default: 0
          },
          'svg': {
              default: '#pen'
          }
        },
        computed: {
          getTitle: function(){
            var value =  Math.round(this.value);
            if(value == 1){
              return 'Einer von Fünf Stiften';
            }else if(value == 2){
              return 'Zwei von Fünf Stiften';
            }else if(value == 3){
              return 'Drei von Fünf Stiften';
            }else if(value == 4){
              return 'Vier von Fünf Stiften';
            }else if(value == 5){
              return 'Fünf von Fünf Stiften';
            }else{
              return 'Null von Fünf Stiften';
            }
          }
        },
        methods: {
          classes: function (nr) {
            var value =  Math.round(this.value);
            if( value >= nr){
              return 'rated';
            }
            return '';
          }
        },
    }
</script>

<style>
svg {
  max-width: 100%;
  max-height: 100%;
  fill: rgba(0, 0, 0, 0.06);
}

.flex-container {
  display: flex;
  flex-direction: row-reverse;
  flex-wrap: wrap;
  justify-content: center;
}

.rating {
  margin-bottom: 20px;
  border: none;
}

.rating label {
  position: relative;
  width: 2em;
  height: 2em;
}

.rating-total svg {
  fill: rgba(0, 0, 0, 0.06);
}

.rating-total .rated svg {
  fill: gold;
}

@media (max-width: 768px) {
  .feedback {
    padding: 20px 20px 40px 20px;
    margin: 20px auto;
    max-width: 90%;
  }
}

@media (min-width: 768px) and (max-width: 1200px) {
  .feedback {
    padding: 30px 30px 40px 30px;
    margin: 50px auto;
    max-width: 70%;
  }
}
</style>
